<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Kolkata');

use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\OrderRepo;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\MasterApiRepo;
use App\models\GdsOrders\GDSOrders;
use App\models\Dmapi\gdsCustomer;
use App\models\Dmapi\gdsProducts;
use App\models\Dmapi\dmapiOrders;
use App\models\Dmapi\Products;
use App\models\Warehouse\warehouseModel;
use App\Modules\Indent\Models\LegalEntity;
use App\Modules\Orders\Models\OrderTrack;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Controllers\OrdersController;
use App\Central\Repositories\MailMongo;
use Notifications;
use PDF;
use Dropbox;
use Session;



/* * ***Use for only queing and the session mangement****** */
use App\Lib\Queue;
use App\models\Mongo\MongoDmapiModel;

class DmapiController extends BaseController {

    protected $roleAccess;
    protected $custRepo;
    protected $apiAccess;
    protected $orderRepo;
    protected $_orderModel;
    protected $MailMongo;
    private $mfgId;
    private $le_warehouses = array();
    private $le_warehouse_id;
    private $order_dump = array();
    private $product_warehouse_dump = array();
    private $queue; //make shift for queue class
    public $channelId;
    public $customerLeId;

    public function __construct(RoleRepo $roleAccess, CustomerRepo $custRepo, MasterApiRepo $ApiAccess, OrderRepo $orderRepo) {
        $this->roleAccess = $roleAccess;
        $this->custRepo = $custRepo;
        $this->apiAccess = $ApiAccess;
        $this->orderRepo = $orderRepo;
        $this->_orderModel = new OrderModel();
        //$this->MailMongo = new MailMongo();
        $this->queue = new Queue();
    }

    /*
      @param type $api_name
      @return type is a string message which tlls you whether the user has permission or not
      Description: This api request is used  to authenticate user and check  user permissions.
     */

    public function checkUserPermission($api_name) {
        $data = Input::get();
        $data['api_name'] = $api_name;

        $mfgId = $this->apiAccess->getManufacturerId($data);

        if ($mfgId) {
            $this->mfgId = $mfgId;
        } else {
            $this->mfgId = NULL;
        }

        $result = $this->apiAccess->apiLogin($data);
        if (isset($result['Status']) && $result['Status'] == 1) {


            $logWrite = $this->putIntorequestLog($api_name, $data);
            if (!$logWrite) {
                return Response::json(['Status' => 0, 'Message' => "Log file failed in dmapi try again"]);
            } else {
                $result = $this->$api_name($data);
                return $result;
            }
        } else {
            return Response::json(['Status' => 0, 'Message' => $result['Message']]);
        }
    }

    /**
     * [checkUserPermissionConsole will call this from the dampi console test]
     * @return [no return ] [ will store in the Mongo db we are storing data]
     */
    public function dmapiConsoleRequest($api_name, $data, $token) {

        $data = $data;
        $data['api_name'] = $api_name;
        $data['token'] = $token;
        $mfgId = $this->apiAccess->getManufacturerId($data);

        if ($mfgId) {
            $this->mfgId = $mfgId;
        } else {
            $this->mfgId = NULL;
        }

        $logWrite = $this->putIntorequestLog($api_name, $data);
        $methodName = $api_name . 'Console';
        $result = $this->$methodName($data);
        $result = $result->getContent();
        $result = json_decode($result, true);
       
        //try for message if fails just go over and update
        if ($api_name == 'placeOrder') {

            if(isset($result['order_id']) && isset($result['order_id_actual'])){

                $order_id = $result['order_id'];
                $order_id_actual = $result['order_id_actual'];

                try {
                    @$ebutorChannelId = Config('dmapi.channelid');
                    if ($ebutorChannelId == $this->channelId) {

                        $data = json_decode($data['orderdata']);
                        $customer_token = isset($data->additional_info->customer_token) ? $data->additional_info->customer_token : null;
                        $fieldforce_token = isset($data->additional_info->sales_token) ? $data->additional_info->sales_token : null;
                        $order_total = $this->order_dump[$order_id_actual]['order_total'];
                        $customer_name = $data->customer_info->first_name;
                        $dmapiOrders = new dmapiOrders();

                        if (!is_null($customer_token) || !empty($customer_token)) {
                            echo "customer_token number is not null";
                            @$mobile_no = $dmapiOrders->getUserMobile($customer_token);
                            if ($mobile_no != 0) {

                                echo "SMS will be sent to " . $mobile_no;
                                if ($result['Status'] == 1) {

                                    date_default_timezone_set('Asia/Kolkata');
                                    $delivery_date = $data->additional_info->scheduled_delivery_date;
                                    $delivery_date = date('Y-m-d', strtotime($delivery_date));
                                    $message = "Your order number is  #$order_id Order Total: $order_total and product will be shipped in 1 day. Scheduled Delivery Date: $delivery_date";
                                    $message_sales = "Your order against customer: $customer_name  has successfully place order id: #" . $order_id . " Ordered Total :  $order_total" ;
                                    $dmapiOrders->cartcancel($customer_token);
                                    $this->slackMsg($message, '#place_orders', $order_id);
                                } else {

                                    $message = "You order could not be placed : " . json_encode($result['Message']) . ' contact support';
                                    $message_sales = "You order against customer $customer_name could not be placed : " . json_encode($result['Message']) . ' contact support';
                                    @$dmapiOrders->updateCart($customer_token, 1);
                                }
                                $dmapiOrders->sendSMS($mobile_no, $message);

                                /**
                                 * Sending SMS to FieldForce
                                 */
                                if (!is_null($fieldforce_token) || !empty($fieldforce_token)) {
                                    @$fieldforce_mobile_no = $dmapiOrders->getUserMobile($fieldforce_token);
                                    if ($fieldforce_mobile_no != "" || $fieldforce_mobile_no != 0) {
                                        $dmapiOrders->sendSMS($fieldforce_mobile_no, $message_sales);
                                    }
                                }
                            }
                        }
                    }
                }
                catch (Exception $e) {
                    echo "Mail or email send failed";
                }
            } 
        }
        return $result;
    }

    /**
     * [putIntorequestLog description]
     * @param  [string] $api_name [Api Name]
     * @param  [string] $data     [Data pushed latter on put this into the mongo central for 
     *                             Work around ]
     * @return [type]           [description]
     */
    public function putIntorequestLog($api_name, $data) {

        try {
            $folder_path = trim(storage_path() . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "DMAPI_logs" . DIRECTORY_SEPARATOR . " ");
            $write_file = $api_name . '.log';
            $file_name = $folder_path . $write_file;
            $write_data = '' . PHP_EOL;
            $write_data .= 'Date:' . date('d-m-Y H:i:s') . PHP_EOL;
            $write_data .= 'Request Data ' . PHP_EOL;
            $write_data .= json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
            $file = fopen($file_name, "a");
            fwrite($file, $write_data);
            fclose($file);
            return true;
        } catch (ErrorException $e) {
            $order_id = 0;
            $message = $e->getTraceAsString();
            return false;
        }
    }

    /*
      @ param type $data
      @ return type product_id,category,category_id,cost_price,manufacturer_id,product_name,sku,attributes
      Description: This API request is used to get the product information based on category_id and channel_ids.
     */

    public function getInventory() {
        try {
//            Log::info(__FUNCTION__ . ' : ' . print_r(Input::get(), true));
            $finalarr = array();
            $finalProductsarr = array();
            $attributesarr = array();
            $category_id = trim(Input::get('category_id'));

            if (!empty($category_id)) {
                $products = DB::table('products')
                        ->select('products.product_id', 'products.product_name as name', 'categories.cat_name as cname', 'categories.category_id', 'product_tot.cost_price', 'products.legal_entity_id as manufacturer_id', 'products.sku')
                        ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
                        ->Join('product_tot', 'products.product_id', '=', 'product_tot.product_id')
                        ->where(array('categories.category_id' => $category_id))
                        ->get()->all();
                if (!empty($products)) {
                    foreach ($products as $key => $value) {
                        $pattr = DB::Table('product_attributes')
                                ->select('product_attributes.value', 'attributes.name')
                                ->leftJoin('attributes', 'attributes.attribute_id', '=', 'product_attributes.attribute_id')
                                ->where(array('product_attributes.product_id' => $value->product_id, 'attributes.attribute_type' => 1))
                                ->get()->all();

                        $temp = array();
                        foreach ($pattr as $key1 => $value1) {
                            $attributesarr[$value1->name] = $value1->value;
                        }
                        $finalProductsarr['product_id'] = $value->product_id;
                        $finalProductsarr['category'] = $value->cname;
                        $finalProductsarr['category_id'] = $value->category_id;
                        $finalProductsarr['cost_price'] = $value->cost_price;
                        $finalProductsarr['manufacturer_id'] = $value->manufacturer_id;
                        $finalProductsarr['product_name'] = $value->name;
                        $finalProductsarr['sku'] = $value->sku;
                        $finalProductsarr['attributes'] = $attributesarr;
                        $finalarr[] = $finalProductsarr;

                        $status = 1;
                        $message = 'Data Successfully Retrieved';
                    }
                } else {
                    $status = 0;
                    $message = 'No Data Retrieved';
                }
            } else {
                throw new Exception('Parameter Missing');
            }
        } catch (Exception $e) {
            $status = 0;
            $message = $e->getMessage();
        }
        //Log::info(['Status' => $status, 'Message' => $message, 'Data' => $finalarr]);
        return Response::json(['Status' => $status, 'Message' => $message, 'Data' => $finalarr]);
    }

    /*
      @ param type $data
      @ return type channel_product_id,channel_id,product_id
      Description: This API request is used to check the product existence.
     */

    public function getProductExistence() {
        try {
          //  Log::info(__FUNCTION__ . ' : ' . print_r(Input::get(), true));
            $product_ids = trim(Input::get('product_id'));
            $prodcheck = array();

            if (empty($product_ids))
                throw new Exception('Paramater Missing');
            //echo "<pre>";print_r($data);die;
            $prodids = explode(',', $product_ids);

            $prodcheck = DB::table('channel_product')
                    ->leftJoin('products', 'products.product_id', '=', 'channel_product.product_id')
                    ->select('channel_product.*')
                    ->whereIn('channel_product.product_id', $prodids)
                    ->get()->all();

            if (!empty($prodcheck)) {
                $status = 1;
                $message = ' Data Successfully Retrieved.';
            } else {
                throw new Exception('Not Successfull');
            }
        } catch (Exception $e) {
            $status = 0;
            $message = $e->getMessage();
        }
        //Log::info(['Status' => $status, 'Message' => $message, 'Data' => $prodcheck]);
        return Response::json(['Status' => $status, 'Message' => $message, 'Data' => $prodcheck]);
    }

    /*
      @ param type $data
      @ return type product_id,available_inventory
      Description: This API request is used to get products inventory based on product_id
     */

    public function getProductsInventory() {
        try {
            //Log::info(__FUNCTION__ . ' : ' . print_r(Input::get(), true));
            $pids = trim(Input::get('pids'));
            $pqty = array();
            if (empty($pids))
                throw new Exception('Paramater Missing');

            $product_ids = explode(',', $pids);

            $pqty = DB::table('product_inventory')
                    ->select('product_inventory.product_id', 'product_inventory.available_inventory')
                    ->leftJoin('products', 'products.product_id', '=', 'product_inventory.product_id')
                    ->where('products.is_gds_enabled', '=', '1')
                    ->whereIn('product_inventory.product_id', $product_ids)
                    ->get()->all();

            if (empty($pqty))
                throw new Exception('Data not found');

            $status = 1;
            $message = 'Data Successfully Retrieved.';
        } catch (Exception $e) {
            $status = 0;
            $message = $e->getMessage();
        }
        //Log::info(['Status' => $status, 'Message' => $message, 'Data' => $pqty]);
        return Response::json(['Status' => $status, 'Message' => $message, 'Data' => $pqty]);
    }

    /*
      @param type $data
      @ return type product_name,description,model_name,upc,ean,jan,isbn,mpn,category_name,cost_price,manufacturer_name,sku,attributes,slab_rates,available_stock
      Description: This API request is used to get product dynamic info. */

    public function getNearestDpsInventory($data) {
        // $image = Input::file('image');
        // $extension = Input::file('image')->getClientOriginalExtension();
        // $name = Input::file('image')->getClientOriginalName();
        // $size = Input::file('image')->getClientSize();
        // $fd = fopen("$image", "rb");
        // $return = Dropbox::connection('main')->uploadFile('/foo/'.$name,Dropbox\WriteMode::add(), $fd, $size);
        $account_info = Dropbox::connection('main')->getHost();
        $fd = fopen('amazon-logo.png', 'w+b');
        $return = Dropbox::connection('main')->getFile('/foo/amazon-logo.png', $fd);
        fclose($fd);
        return Response::json(['Status' => 200, 'Message' => $return, 'Account' => $account_info]);
    }

    /**
     * printInoice Dmapi for printing and downloading incoice
     * Version: 1.0
     * Platform: DMAPI Version 1.0
     * Creat Date: 19th August 2016
     * @param  json $orderdata holds the request data in json format
     * @return ("Content-type:application/pdf")
     */
     public function printInvoice($orderdata) {
        try {
            $_leModel = new LegalEntity();
            $_OrderTrack = new OrderTrack();
            header("Content-type:application/pdf");
            $data = json_decode($orderdata['orderdata'], true);
            $invoiceId = $data['invoiceId'];
            $orderId = $data['orderId'];
            /**
             * Defining invoice type (by default 1 for api call)
             * @var integer
             */
            $invoice_type = 1;
            //New files
            if($invoice_type==1){
                $products = $this->_orderModel->getInvoiceProductsById($invoiceId,$orderId);
            }
            else{
                $products = $this->_orderModel->getInvoiceProductByOrderId($invoiceId);
            }

            if(isset($products[0]->gds_order_id)){
                $orderId = $products[0]->gds_order_id;
                $orderDetails = $this->_orderModel->getOrderDetailById($orderId);
                if(count($orderDetails)==0) {
                    Redirect::to('/salesorders/index')->send();
                }
                $trackInfo = $_OrderTrack->getTrackDetailByOrderId($orderId);
                $taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);
                $billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
                $billingAndShipping = $this->convertBillingAndShippingAddress($billingAndShippingArr);
                $legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id,$orderDetails->le_wh_id);

                $taxSummaryArr = $this->_orderModel->getTaxSummary($taxArr);
                $taxSummary = isset($taxSummaryArr['summary']) ? $taxSummaryArr['summary'] : '';
                $productTaxArr = isset($taxSummaryArr['item']) ? $taxSummaryArr['item'] : '';
                $taxBreakup = isset($taxSummaryArr['breakup']) ? $taxSummaryArr['breakup'] : '';
                $leInfo = $_leModel->getLegalEntityById($orderDetails->legal_entity_id);
                $lewhInfo = $_leModel->getWarehouseById($orderDetails->le_wh_id);
                $prodTaxes = array();
                
                foreach ($taxArr as $tax) {
                    $prodTaxes[$tax->product_id] = array('name'=>$tax->name, 'tax_value'=>$tax->tax_value, 'tax'=>$tax->tax);
                }
                $companyInfo = $_leModel->getCompanyAccountByLeId($orderDetails->legal_entity_id);
                $userInfo = '';
                if($orderDetails->created_by) {
                    $userInfo = $_leModel->getUserById($orderDetails->created_by);
                }

                /**
                 * $billing billing address details
                 * @var array
                 */
                $billing = $billingAndShipping['billing'];
                /**
                 * $shipping holds shipping address detials
                 * @var array
                 */
                $shipping = $billingAndShipping['shipping'];
                /**
                 * $pdf init pdf class by calling view
                 * @var content-type:PDF
                 */
                $return_val                 = array();

                $return_val['billing']       = $billingAndShipping['billing'] ;
                $return_val['products']      = $products ; 
                $return_val['shipping']      = $billingAndShipping['shipping'] ; 
                $return_val['taxArr']        = $productTaxArr ;
                $return_val['taxSummaryArr'] = $taxArr ; 
                $return_val['leInfo']        = $leInfo ; 
                $return_val['lewhInfo']      = $lewhInfo ;
                $return_val['companyInfo']   = $companyInfo ;
                $return_val['taxBreakup']    = $taxBreakup ;
                $return_val['trackInfo']     = $trackInfo ; 
                $return_val['orderDetails']  = $orderDetails ; 
                $return_val['legalEntity']   = $legalEntity ; 
                $return_val['userInfo']      = $userInfo ; 
                $return_val['prodTaxes']     = $prodTaxes;

                return $this->returnJsonMessage(1, $return_val);

                //$pdf = PDF::loadView('invoice.dmapiInvoice', compact('orderDetails','products','billing','shipping','taxArr','legalEntity'));
                /**
                 * return $pdf->stream('invoice.pdf'); //to display in browser
                 */
                //return $pdf->download('invoice.pdf');            
            } else {
                $status = 0;
                $responseData = array();
                $message = 'No invoice yet';
                return $this->returnData($status, $message, $responseData);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     *   @ was named as checkInventoryAvailability will change it to getrequestsStatus  
     * */
    public function checkInventoryAvailability($data) {

        $MongoDmapiModel = new MongoDmapiModel();
        if (isset($data['type'])) {

            if ($data['type'] == 'singleKey') {

                $id = $data['id'];
                $data = $MongoDmapiModel->getDataById($id);

                var_dump($data);
            } else if ($data['type'] == 'rangeQuery') {

                $today = date("Y-m-d");
                $pageNo = isset($data['pageNo']) ? $data['pageNo'] : 1;
                $limit = 50;
                $startDate = isset($data['startDate']) ? $data['startDate'] : $today;
                $endDate = isset($data['endDate']) ? $data['endDate'] : $today;
                $data = $MongoDmapiModel->getDmapiListDate($startDate . " 00:00:00", $endDate . " 23:59:59", $limit, $pageNo);

                var_dump($data);
                exit;
            } else {

                $status = 0;
                $message = 'No Type set check again';
            }
        } else {

            $status = 0;
            $message = 'No Type set check again';
        }

        return $this->returnJsonMessage($status, $message);
    }

    public function checkInventoryAvailability123($data) {
        try {
            $status = 0;
            $message = '';
            $p_data = json_decode($data['product_data'], true);
            // $gds_order_token = $data['order_token'];
            $return_token = '';
            if (!empty($p_data)) {
                $manufacturerId = $this->apiAccess->getManufacturerId($data);
                //$ppid = (isset($data['ppid']) && $data['ppid'] != '') ? $data['ppid'] : '';
                $pincode = (isset($data['pincode']) && $data['pincode'] != '') ? $data['pincode'] : 0;
                $locationData = $this->getZonebyPincode($pincode, $manufacturerId);
                $locationDetails = json_decode($locationData);
                //echo "<pre>";print_R($locationDetails);die;
                $locationId = $locationDetails->location_id;
                $ppid = $locationId;

                $product_data = array();
                $sku = isset($p_data['sku']) ? $p_data['sku'] : '';
                $quantity = isset($p_data['quantity']) ? $p_data['quantity'] : 0;
                $price = isset($p_data['price']) ? $p_data['price'] : 0;
                $total = isset($p_data['total']) ? $p_data['total'] : 0;
                $product_ids = DB::table('products')
                        ->select('products.product_id', 'products.sku', 'eseal_customer.customer_id')
                        ->leftJoin('eseal_customer', 'eseal_customer.customer_id', '=', 'products.legal_entity_id')
                        ->where('products.sku', $sku)
                        ->get()->all();
                $last = DB::getQueryLog();
                if (!empty($product_ids)) {
                    $pids['product_id'] = $product_ids[0]->product_id;
                    $pids['sku'] = $product_ids[0]->sku;
                    $pids['customer_id'] = $product_ids[0]->customer_id;
                    $pids['qty'] = $quantity;
                    $pids['price'] = $price;
                    $pids['total'] = $total;
                    $product_data[] = $pids;
                }
                //print_r($product_data);exit;
                if (empty($product_data)) {
                    return Response::json(Array('Status' => 0,
                                'Message' => 'No Product'));
                }
                $tmpProductAvailablearr = array();
                $productAvailablearr = array();
                $reqProductAvailablearr = array();
                $available = 1;
                foreach ($product_data as $key => $value) {
                    $pdb = 'eseal_' . $value['customer_id'];
                    $qty = $value['qty'];
                    if (!empty($ppid)) {
                        try {
                            $pqty = DB::table($pdb)
                                    ->select($pdb . '.primary_id')
                                    ->leftJoin('track_history', 'track_history.track_id', '=', $pdb . '.track_id')
                                    ->where(array($pdb . '.level_id' => 0, $pdb . '.pid' => $value['product_id'], $pdb . '.gds_status' => 0, 'track_history.src_loc_id' => $ppid, 'dest_loc_id' => 0))
                                    ->groupBy($pdb . '.pid')
                                    ->count();
                        } catch (ErrorException $e) {
//                            Log::info($e->getMessage());
                            $message = $e->getMessage();
                            //throw new Exception($message);
                        }

                        if ($qty > $pqty) {
                            $available = 0;
                            $tmpProductAvailablearr['pid'] = $value['product_id'];
                            $tmpProductAvailablearr['sku'] = $value['sku'];
                            $tmpProductAvailablearr['qty'] = $pqty;
                            $reqProductAvailablearr[] = $tmpProductAvailablearr;
                        } else {
                            $tmpProductAvailablearr['pid'] = $value['product_id'];
                            $tmpProductAvailablearr['sku'] = $value['sku'];
                            $tmpProductAvailablearr['qty'] = $pqty;
                            $reqProductAvailablearr[] = $tmpProductAvailablearr;
                        }
                    } else {
                        $pqty = DB::table($pdb)
                                ->select($pdb . '.primary_id')
                                ->where(array($pdb . '.level_id' => 0, $pdb . '.gds_status' => 0, $pdb . '.pid' => $value['product_id']))
                                ->groupBy($pdb . '.pid')
                                ->count();
                        $last = DB::getQueryLog();
                        //print_R(end($last));die;
                        if ($qty > $pqty) {
                            $available = 0;
                            $tmpProductAvailablearr['pid'] = $value['product_id'];
                            $tmpProductAvailablearr['sku'] = $value['sku'];
                            $tmpProductAvailablearr['qty'] = $pqty;
                            $reqProductAvailablearr[] = $tmpProductAvailablearr;
                        } else {
                            $tmpProductAvailablearr['pid'] = $value['product_id'];
                            $tmpProductAvailablearr['sku'] = $value['sku'];
                            $tmpProductAvailablearr['qty'] = $pqty;
                            $reqProductAvailablearr[] = $tmpProductAvailablearr;
                        }
                    }
                }
                $productAvailablearr = $reqProductAvailablearr;
                if ($available == 1) {
                    $is_blocked = (isset($data['is_blocked']) && $data['is_blocked'] != '') ? $data['is_blocked'] : '';
                    if ($is_blocked == 1 && $ppid > 0) {
                        //echo 'hi';exit;
                        //$access_token = $data['access_token']; 
                        //echo "<pre/>";print_r($data['orderdata']);exit;
                        //Finding whether this order token is valid or not
                        /*                        $validOrder = DB::table('users_token')
                          ->select('users.customer_id')
                          ->leftJoin('users','users.user_id','=','users_token.user_id')
                          ->where(array('users_token.access_token'=>$access_token))
                          ->get()->all(); */
                        $customer_id = $this->apiAccess->getManufacturerId($data);
                        //echo '<pre/>';print_r($validOrder[0]->customer_id);exit;
                        //$customer_id = $validOrder[0]->customer_id;

                        $order_token = $this->apiAccess->getuuid();
                        $dm_order_token = new DmOrderToken;
                        $dm_order_token->customer_id = $customer_id;
                        $dm_order_token->order_token = $order_token[0]->uuid;
                        $dm_order_token->date_time = date('Y-m-d h:i:s');
                        //$dm_order_token->user_agent=$data['user_agent'];
                        //echo "<pre>";print_r($dm_order_token);die;
                        $dm_order_token->save();
                        $return_token = $order_token[0]->uuid;
                        $subOrderGroupArr = array();
                        foreach ($product_data as $key => $value) {
                            $pdb = 'eseal_' . $value['customer_id'];
                            $qty = $value['qty'];
                            //Update the stock for blocking
                            $Sql = "select e1.eseal_id  from " . $pdb . " e1 , track_history th  
                                    where th.track_id=e1.track_id and e1.gds_status=0 and e1.level_id=0 and e1.pid=" . $value['product_id'] . " and th.src_loc_id=" . $ppid . " and th.dest_loc_id=0 LIMIT " . $qty;
                            //echo $Sql;die;
                            $results = DB::select($Sql);
                            $temp = array();
                            foreach ($results as $result) {
                                $temp [] = $result->eseal_id;
                            }
                            //echo "<pre/>";print_r($temp);exit;
                            DB::table($pdb)
                                    ->where(array('pid' => $value['product_id'], 'level_id' => 0, 'gds_status' => 0))
                                    ->whereIn('eseal_id', $temp)
                                    ->update(array('gds_status' => 1, 'gds_order' => $order_token[0]->uuid));
                        }
                    }
                    $status = 1;
                    $message = 'Stock is available.';
                } else {
                    $order_token = '';
                    $message = 'Out of Stock for the following products.';
                }
            } else {
                $status = 0;
                $message = 'Parameter Missing.';
                //throw new Exception($message);
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            //echo "<pre>";echo $message;print_R($e->getTraceAsString());die;
        }
        return Response::json(Array('Status' => $status, 'Message' => $message, 'order_token' => $return_token, 'Data' => $productAvailablearr));
    }

    public function unblockInventory($data) {
        try {
            $status = 0;
            $message = '';
            $p_data = json_decode($data['product_data'], true);
            $gds_order_token = $data['order_token'];

            $return_token = '';
            if (!empty($p_data)) {
                $ppid = (isset($data['ppid']) && $data['ppid'] != '') ? $data['ppid'] : '';

                foreach ($p_data as $key => $value) {
                    $sku = $p_data['sku'];
                    $product_ids = DB::table('products')
                            ->select('products.product_id', 'products.sku', 'legal_entities.legal_entity_id')
                            ->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'products.legal_entity_id')
                            ->where('products.sku', $sku)
                            ->get()->all();
                    if (!empty($product_ids)) {
                        $pids['product_id'] = $product_ids[0]->product_id;
                        $pids['sku'] = $product_ids[0]->sku;
                        $pids['legal_entity_id'] = $product_ids[0]->legal_entity_id;
                        $pids['qty'] = $value['quantity'];
                        $pids['price'] = $value['price'];
                        $pids['total'] = $value['total'];
                        $product_data[] = $pids;
                    } else {
                        $product_data[] = "";
                    }
                }

                $available = 1;
                foreach ($product_data as $key => $value) {
                    /* $pdb = 'eseal_' . $value['legal_entity_id'];
                      $qty = $value['qty'];

                      DB::table($pdb)
                      ->where(array('pid' => $value['product_id'], 'level_id' => 0, 'gds_status' => 1, 'gds_order' => $gds_order_token))
                      ->update(array('gds_status' => 0, 'gds_order' => 'unknown', 'gds_sub_order' => 'unknown')); */
                    $message = 'Quantity is ublocked.';
                }
            } else {
                $status = 0;
                $message = 'Parameter Missing.';
                //throw new Exception($message);
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return Response::json(Array('Status' => $status, 'Message' => $message));
    }

   

    /**
     * 
     * @param type $data
     * @return type $data
     * @Description:
     */
    public function categoryCount($data) {
        return $data;
    }

    /**
     * 
     * @param type $data
     * @return type $data
     * @Description:
     */
    public function productCount($data) {
        return $data;
    }

    /**
     * 
     * @param type $data
     * @return type $data
     * @Description:
     */
    public function returnOrder($data) {

        try {

            $order_data = json_decode($data['orderdata']);
            $_orderModuleModel = $this->_orderModel;
            if (empty($order_data)) {
                $status = 0;
                $message = 'Request Format not correct.';
                return $this->returnJsonMessage($status, $message);
            }

            if (!isset($order_data->orderid) && !isset($order_data->channelorderid)) {
                $status = 400;
                $message = "['mandatory field'] Both Orderid and ChannelOrderId is missing, Either one should be present";
                return $this->returnJsonMessage($status, $message);
            }

            if (!isset($order_data->orderitems)) {

                $status = 400;
                $message = "['mandatory field'] Product Info is missing..";
                return $this->returnJsonMessage($status, $message);
            }

            if (!isset($order_data->channelid)) {

                $status = 400;
                $message = "['mandatory field'] Channelid Info is missing..";
                return $this->returnJsonMessage($status, $message);
            } else {
                $channelId = $order_data->channelid;
            }

            if (isset($order_data->orderid) && property_exists($order_data, 'orderid')) {
                $orderId = $order_data->orderid;
                $orders = $_orderModuleModel->getOrderDetailById($orderId);
            } else if (isset($order_data->channelorderid) && property_exists($order_data, 'channelorderid')) {
                $orderId = $order_data->orderid;
                $orders = $_orderModuleModel->getOrderDetailByChannelOrderId($channelOrderId, $mp_id);
            } else {
                $status = 400;
                $message = "['mandatory field'] Both Orderid or ChannelOrderId is missing, Either one should be present";
                return $this->returnJsonMessage($status, $message);
            }

            if (!$orders) {

                $status = 404;
                $message = "OrderId / ChannelOrderId not found ..";
                return $this->returnJsonMessage($status, $message);
            }

            //var_dump($orders);
            $orderId = $orders->gds_order_id;

            $products = $_orderModuleModel->getProductByOrderId($orderId);

            $products_list_key = array();
            $order_skus = array();
            foreach ($products as $product) {

                $products_list_key[$product->sku] = $product->product_id;
                $order_skus[] = $product->sku;
            }

            if (count($order_skus) == 0 || count($products_list_key) == 0) {
                $status = 404;
                $message = "Order does not contain any skus";
                return $this->returnJsonMessage(500, $message);
            }

            $channelId = $order_data->channelid;
            $order_status_available = array('17001', '17006', '17007', '17008');
            $ebutor_order_status_id = $orders->order_status_id;

            if (!in_array($ebutor_order_status_id, $order_status_available)) {

                $message = "Order should in following state to be returned Delivered/Shipped/Complete";
                return $this->returnJsonMessage(400, $message);
            }

            $default_return_reason_id = 60001;
            $shippedProductQtyArr = $_orderModuleModel->getShipmentQtyByOrderId($orderId);
            $availShippedQtyArr = $_orderModuleModel->getAvailableShippedQty($products, $shippedProductQtyArr);

            $shippedArray = array();
            $returnArray = array();
            $errorShipped = array();
            foreach ($order_data->orderitems as $shipmentProducts) {

                if (array_key_exists($shipmentProducts->product_id, $shippedProductQtyArr)) {

                    $returnArray[$shipmentProducts->product_id] = $shipmentProducts->quantity;

                    if ($shippedProductQtyArr[$shipmentProducts->product_id] < $shipmentProducts->quantity) {

                        $errorShipped[] = "sku " . $shipmentProducts->sku . " quantity " . $shipment->quantity . "more than actual shipped ";
                    }

                    if ($shipmentProducts->quantity <= 0) {

                        $errorShipped[] = "sku " . $shipmentProducts->sku . " return quantity has to be more than 0";
                    }
                } else {
                    $errorShipped[] = "sku " . $shipmentProducts->sku . " is not found in any shippment";
                }
            }

            if (count($errorShipped) > 0) {
                return $this->returnJsonMessage(500, $errorShipped);
            } else {

                $returnGridId = $_orderModuleModel->saveReturnGrid(array('gds_order_id' => $orderId, 'status_id' => $ebutor_order_status_id));

                foreach ($order_data->orderitems as $value) {

                    $productId = $value->product_id; //$products_list_key[$sku];
                    $data = array(
                        'return_grid_id' => $returnGridId,
                        'product_id' => $productId,
                        'qty' => $value->quantity,
                        'status_id' => $ebutor_order_status_id,
                        'gds_order_id' => $orderId,
                        'return_status_id' => isset($value->returnreasonid) ? $value->returnreasonid : $default_return_reason_id
                    );
                    $_orderModuleModel->saveReturnGridItem($data);
                }
                $message = "Return created Sucessfully";
                return $this->returnJsonMessage(1, $message);
            }
        } catch (Exception $e) {

            $message = $e->getMessage() . ' - ' . $e->getTraceAsString();
            return $this->returnJsonMessage(500, $message);
        }
    }

    /**
     * 
     * @param type $data
     * @return type $data
     * @Description:
     */
    public function updateOrderStatus($data) {
        $orderDaTa = json_decode($data['orderdata'], true);

        //  
        if (empty($orderDaTa)) {
            $status = 0;
            $message = 'Request Format not correct.';
            return Response::json(array('Status' => $status, 'Message' => $message));
        }
        if (isset($orderDaTa['ChannelOrderID']) || isset($orderDaTa['OrderId'])) {
            $customerAddress = isset($orderDaTa['address_info']) ? $orderDaTa['address_info'] : array();
            //Log::info('customerAddress');
            //Log::info($customerAddress);
            if (!empty($customerAddress)) {
                foreach ($customerAddress as $address) {
                    $cust = $this->customerAddressupdate($address, $orderDaTa);
                }
            }

            // check the status in the mapping_status table
            $orderstatus = isset($orderDaTa['order_info']['orderstatus']) ? $orderDaTa['order_info']['orderstatus'] : array();
            $checkmapstatus = DB::table('mp_status_mapping as ms')
                    ->select('*')
                    ->where('mp_status', $orderstatus)
                    ->first();
            if (!empty($checkmapstatus->ebutor_status_id)) {
                $checkStatus = DB::table('master_lookup as ml')
                        ->select('*')
                        ->join('master_lookup_categories as mlc', 'mlc.mas_cat_id', '=', 'ml.mas_cat_id')
                        ->where('mlc.mas_cat_name', '=', 'Order Status')
                        ->where('ml.value', '=', $checkmapstatus->ebutor_status_id)
                        ->first();
                if (isset($checkStatus->master_lookup_name)) {
                    // update the status into the order table
                    if (isset($orderDaTa['OrderId'])) {
                        $orderid = $orderDaTa['OrderId'];
                        $updateStatus = DB::table('gds_orders')
                                ->where('gds_order_id', $orderid)
                                ->update(array('order_status_id' => $checkStatus->value));
                    } else {
                        $orderid = $orderDaTa['ChannelOrderID'];
                        $updateStatus = DB::table('gds_orders')
                                ->where('mp_order_id', $orderid)
                                ->update(array('order_status_id' => $checkStatus->value));
                    }

                    // if order not updated because of Order ID mismatched
                    if (!$updateStatus) {
                        return Response::json(Array('Status' => 304, 'Message' => 'Status not updated due to order id mismatched or Status is Unchanged!',));
                    } else {
                        $args = array("ConsoleClass" => 'mail', 'arguments' => array('DmapiOrderUpdateTemplate', $orderid));
                        $token = $this->queue->enqueue('default', 'ResqueJobRiver', $args);

                        \Notifications::addNotification(['note_code' => 'ORD001', 'note_message' => 'Order #ORDID updated Successfully', 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['ORDID' => $orderid]]);
                        return Response::json(Array('Status' => 200, 'Message' => 'Status updated Successfully.',));
                    }
                } else {
                    // if status code not found in the loopup table
                    return Response::json(Array('Status' => 404, 'Message' => 'Status code not found, Update failed',));
                }
            } else {
                return Response::json(Array('Status' => 304, 'Message' => 'Cannot find the mapped Ebutor status of the given status',));
            }
        } else {
            return Response::json(Array('Status' => 404, 'Message' => 'Please provide either ChannelOrderId or gdsOrderId',));
        }
    }

    public function customerAddressupdate($data = null, $orderdata = null) {
        try {
            if (isset($orderdata['OrderId'])) {
                $orderid = $orderdata['OrderId'];
            } else {
                $orderid = $orderdata['ChannelOrderID'];
            }

            $custAddressId = DB::table('gds_orders_addresses')
                            ->where(array('address_type' => $data['address_type'],
                                'gds_order_id' => $orderid))->pluck('gds_addr_id')->all();
            if (count($custAddressId) > 0)
                $custAddressId = $custAddressId[0];
            else
                $custAddressId = "";


            $state_id = DB::table('zone')->where('name', $data['state'])->pluck('zone_id')->all();
            if (count($state_id) > 0)
                $state_id = $state_id[0];
            else
                $state_id = 0;
            $country_id = DB::table('countries')->where('name', $data['country'])->pluck('country_id')->all();
            if (count($country_id) > 0)
                $country_id = $country_id[0];
            else
                $country_id = 0;

            $custAddressArray = [
                'fname' => isset($data['first_name']) ? $data['first_name'] : '',
                'mname' => isset($data['middle_name']) ? $data['middle_name'] : '',
                'lname' => isset($data ['last_name']) ? $data['last_name'] : '',
                'address_type' => isset($data['address_type']) ? $data['address_type'] : '',
                'company' => isset($data['company']) ? $data['company'] : '',
                'addr1' => isset($data['address1']) ? $data['address1'] : '',
                'addr2' => isset($data['address2']) ? $data['address2'] : '',
                'city' => isset($data['city']) ? $data['city'] : '',
                'state_id' => $state_id,
                'country_id' => $country_id,
                'postcode' => isset($data['pincode']) ? $data['pincode'] : '',
                'telephone' => isset($data['phone']) ? $data['phone'] : '',
                'mobile' => isset($data['mobile_no']) ? $data['mobile_no'] : '',
                'gds_order_id' => $orderid
            ];
            $update = DB::table('gds_orders_addresses')
                    ->where(array('address_type' => $data['address_type'],
                        'gds_order_id' => $orderid))
                    ->update($custAddressArray);
            if ($update) {
                $status = 1;
                $message = "Successfully updated";

                return json_encode(Array('Status' => $status, 'Message' => $message));
            } else {
                $status = 0;
                $message = "Update of address failed due to incorrect orderid";

                return json_encode(Array('Status' => $status, 'Message' => $message));
            }
        } catch (ErrorException $ex) {
            return $ex->getMessage() . $ex->getTraceAsString();
        }
    }

    public function placeBackOrder($data) {
        return $data;
    }

    public function checkIsChannel($data) {
        try {
            $apikey = isset($data['api_key']) ? $data['api_key'] : '';
            $secretkey = isset($data['secret_key']) ? $data['secret_key'] : '';
            $isChannel = DB::table('api_session')->where(['api_key' => $apikey, 'secret_key' => $secretkey])->pluck('is_mp')->all();
            if (count($isChannel) > 0)
                $isChannel = $isChannel[0];
            else
                $isChannel = "";
            return $isChannel;
        } catch (ErrorException $ex) {
            Log::info($ex->getMessage());
        }
    }

    /*
      @param type $data
      @return type product_name,description,model_name,upc,ean,jan,isbn,mpn,categories,mrp,cost_price,sku,attributes
      Description : This API request gets the manufacturer specific products in a limit that are sorted on product_id */

    public function getAllInventory($data) {
        try {
            $finalProductsarr = array();
            $finaltempProducts = array();
            $count = 0;
            $isChannel = $this->checkIsChannel($data);

            $start_limit = (isset($data['start_limit']) && $data['start_limit'] != '') ? $data['start_limit'] : 0;
            $end_limit = (isset($data['end_limit']) && $data['end_limit'] != '') ? $data['end_limit'] : 0;
            $orderBy = (isset($data['order_by']) && $data['order_by'] != '') ? $data['order_by'] : 'ASC';
            $productType = (isset($data['parents']) && $data['parents'] != '') ? $data['parents'] : 0;
            $channelCategoryId = (isset($data['category_id']) && $data['category_id'] != '') ? $data['category_id'] : 0;
            $fromDate = (isset($data['from_date']) && $data['from_date'] != '') ? $data['from_date'] : date('Y-m-d') . ' 00:00:00';
            $toDate = (isset($data['to_date']) && $data['to_date'] != '') ? $data['to_date'] : date('Y-m-d H:i:s');
            $categoryId = 0;
            $channelId = DB::table('api_session')
                    ->where(array('api_key' => $data['api_key'], 'secret_key' => $data['secret_key']))
                    ->pluck('mp_id')->all();
            if (count($channelId) > 0)
                $channelId = $channelId[0];
            else
                $channelId = 0;
            if ($channelCategoryId > 0) {
                $categoryId = DB::Table('channel_categories')
                        ->where(['channel_id' => $channelId, 'channel_category_id' => $channelCategoryId])
                        ->pluck('ebutor_category_id')->all();
                if (count($categoryId) > 0)
                    $categoryId = $categoryId[0];
                else
                    $categoryId = 0;
            }
            $limit = 500;
            if ($start_limit == 0 && $end_limit > 0) {
                if ($end_limit > $limit) {
                    $start_limit = ($end_limit - $limit);
                } else {
                    $limit = $end_limit;
                }
            } elseif ($start_limit > 0 && $end_limit == 0) {
                //nothing to do this works fine
            }if ($start_limit > 0 && $end_limit > 0) {
                if ($end_limit > $start_limit) {
                    if (($end_limit - $start_limit) < $limit) {
                        $limit = ($end_limit - $start_limit);
                    }
                }
            }
            $manufacturer_id = $this->apiAccess->getManufacturerId($data);

            //products.ean, products.jan,products.isbn,products.mpn, -- removed
            $productQuery = "select 
                products.product_id, products.product_title as name, products.sku, products.category_id, 
                product_content.description, products.upc,product_tot.rlp as b2b_unit_price, product_tot.dlp as dealer_unit_price,
                products.upc_type, product_tot.distributor_margin as b2b_margin_percentage,products.no_of_units, mp_category_mapping.category_id, "
                    . "product_inventory.available_inventory from products "
                    . "left join `product_inventory` on `product_inventory`.`product_id` = `products`.`product_id` "
                    . "left join `mp_product_add_update` on `mp_product_add_update`.`product_id` = `products`.`product_id` "
                    . "JOIN `product_relations` ON `product_relations`.`parent_id` = mp_product_add_update.`product_id` "
                    . "left join `mp_category_mapping` on `mp_category_mapping`.`category_id` = `products`.`category_id`"
                    . "JOIN `product_tot` ON `products`.`product_id` = product_tot.`product_id` "
                    . "JOIN `product_content` ON `products`.`product_id` = product_content.`product_id` "
                    . "where (`products`.`is_gds_enabled` = 1 and `products`.`is_deleted` = 0 "
                    . "and `products`.`is_active` = 1) "
                    . "and `product_inventory`.`available_inventory` is not null ";

            if ($categoryId > 0) {
                $productQuery = $productQuery . " and products.category_id = " . $categoryId;
            }
            if ($isChannel) {
                $productQuery = $productQuery . " and mp_product_add_update.mp_id = " . $channelId;
            }
            if (!empty($manufacturer_id) && !$isChannel) {
//                $products = $products->where('products.legal_entity_id', $manufacturer_id);
                $productQuery = $productQuery . " and products.legal_entity_id = " . $manufacturer_id;
            }
            if ($productType) {
                $productQuery = $productQuery . " and products.is_parent = 1";
            }
            $productQuery = $productQuery . " AND products.created_at BETWEEN '" . $fromDate . "' AND '" . $toDate . "' 
  OR products.updated_at BETWEEN '" . $fromDate . "' AND '" . $toDate . "' ";
            $productQuery = $productQuery . " group by `products`.`product_id` "
                    . "order by `products`.`product_id` " . $orderBy . ' '
                    . " limit " . $start_limit . ", " . $limit;
           // Log::info('productQuery');
            //Log::info($productQuery);
            $products = DB::select(DB::raw($productQuery));
            if (!empty($products)) {
                $count = count($products);
                foreach ($products as $key => $value) {
                    if (!empty($value->sku)) {
                        $categories = explode(',', $value->category_id);
                        $channel_value = DB::table('api_session')->where(array('api_key' => $data['api_key'], 'secret_key' => $data['secret_key']))->first();
                        $prodCatarr = DB::Table('mp_categories')
                                        ->join('mp_category_mapping', 'mp_category_mapping.mp_category_id', '=', 'mp_categories.mp_category_id')
                                        ->whereIn('mp_category_mapping.category_id', $categories)
                                        ->where('mp_categories.mp_id', $channel_value->channel_id)
                                        ->select('category_name', 'mp_category_id')->first();

                        $finalarr = array();
                        $finalStaticsarr = array();
                        $finalSlabarr = array();
                        $finalMediaarr = array();
                        $prodStaticattr = DB::Table('product_attributes')
                                ->select('product_attributes.value', 'attributes.attribute_code', 'attributes.name', 'attributes.attribute_id', 'attributes.attribute_group_id', 'attributes_groups.name as attribute_group_name', 'attributes.is_varient')
                                ->leftJoin('attributes', 'attributes.attribute_id', '=', 'product_attributes.attribute_id')
                                ->leftJoin('attributes_groups', 'attributes_groups.attribute_group_id', '=', 'attributes.attribute_group_id')
                                ->where(array('product_attributes.product_id' => $value->product_id, 'attribute_type' => 1))
                                ->get()->all();

                        $StaticAttributesarr = array();
                        $attributeGrouparr = array();
                        $varientAttributes = array();
                        $varientQuery = DB::Table('product_attributes')
                                ->leftJoin('attributes', 'attributes.attribute_id', '=', 'product_attributes.attribute_id')
                                ->where(array('product_attributes.product_id' => $value->product_id, 'is_varient' => 1))
                                ->pluck(DB::raw('group_concat(attributes.attribute_code) as attribute_code'))->all();
                        if (!empty($varientQuery) > 0) {
                            $varientAttributes[] = $varientQuery;
                        }
                        foreach ($prodStaticattr as $key1 => $value1) {
                            //$StaticAttributesarr[$value1->name] = $value1->value;
                            if (in_array($value1->attribute_group_name, $attributeGrouparr)) {
                                array_push($attributeGrouparr[$value1->attribute_group_name][$value1->name], $value1->value);
                            } else {
                                $attributeGrouparr[$value1->attribute_group_name][$value1->name] = $value1->value;
                            }
                        }

                        $childData = $this->getChildProductDynamicInfo($value->sku);

                        $finalarr['product_name'] = $value->name;
                        $finalarr['description'] = $value->description;
                        //$finalarr['model_name'] = $value->model_name;
                        $finalarr['upc_type'] = $value->upc_type;
                        $finalarr['upc'] = $value->upc;
                        //$finalarr['ean'] = $value->ean;
                        //$finalarr['jan'] = $value->jan;
                        //$finalarr['isbn'] = $value->isbn;
                        //$finalarr['mpn'] = $value->mpn;
                        $finalarr['categories'] = $prodCatarr;
                        $finalarr['mrp'] = $value->mrp;
                        $finalarr['cost_price'] = $value->cost_price;
                        // $finalarr['manufacturer_name']=$value->brand_name;              
                        $finalarr['sku'] = $value->sku;
                        $finalarr['attributes'] = $attributeGrouparr;
                        $finalarr['quantity'] = $value->available_inventory;
                        $finalarr['browsenode'] = "5";
                        $finalarr['min_order_quantity'] = ($value->min_order_quantity != null) ? $value->min_order_quantity : 1;
                        $finalarr['margin_price'] = $value->b2b_margin_percentage;
                        $finalarr['per_unit_price'] = $value->b2b_unit_price;
                        $finalarr['dealer_unit_price'] = $value->dealer_unit_price;
                        $finalarr['no_of_units'] = $value->no_of_units;
                        //$finalarr['dealer_price'] = $value->mrp-(($value->mrp/100)*$value->margin_price);
                        $finalarr['dealer_price'] = round(($value->mrp * 100) / (100 + $value->b2b_margin_percentage), 2);

                        //Get Products Image Data              
                        $media = DB::table('product_media')
                                ->select('product_media.media_type', 'product_media.url')
                                ->leftJoin('products', 'products.product_id', '=', 'product_media.product_id')
                                ->where('product_media.product_id', $value->product_id)
                                ->get()->all();
                        $doc_root = $_SERVER['SERVER_NAME'] . '/uploads/products/';

                        $mediaarr = array();
                        foreach ($media as $key3 => $value3) {
                            if (!empty($value3->media_type)) {
                                if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value3->url))
                                    $mediaarr[$value3->media_type][] = $value3->url;
                                else
                                    $mediaarr[$value3->media_type][] = $doc_root . $value3->url;
                            }
                        }
                        $finalarr['media'] = $mediaarr;

                        $finalSlabarr = $this->getSlabRates($value->product_id, $value->mrp, $value->b2b_margin_percentage, $value->b2b_unit_price);
                        $finalarr['slab_rates'] = $finalSlabarr;
                        $finalarr['varient_fields'] = (empty($childData)) ? [] : (!empty($varientAttributes) ? implode(',', $varientAttributes) : '');
                        $finalarr['varients'] = (empty($childData)) ? [] : $childData;
                        $finaltempProducts[] = $finalarr;
                    }
                }
                $status = 1;
                $message = 'Data Successfully Retrieved.';
            }else {
                $status = 1;
                $message = 'Empty records.';
            }
        } catch (ErrorException $e) {
            $status = 0;
            $message = $e->getMessage();
            //Log::info($e->getTraceAsString());
        }
        return json_encode(Array('Status' => $status, 'Message' => $message, 'Data' => $finaltempProducts, 'count' => $count));
    }

    /*
      @param type $data
      @return type product_name,description,model_name,upc,ean,jan,isbn,mpn,categories,mrp,cost_price,sku,attributes
      Description : This API request gets the manufacturer specific products in a limit that are sorted on product_id */

    public function getAllProducts($data) {
        try {
            $finalProductsarr = array();
            $finaltempProducts = array();
            $count = 0;
            $isChannel = $this->checkIsChannel($data);

            $start_limit = (isset($data['start_limit']) && $data['start_limit'] != '') ? $data['start_limit'] : 0;
            $end_limit = (isset($data['end_limit']) && $data['end_limit'] != '') ? $data['end_limit'] : 0;
            $orderBy = (isset($data['order_by']) && $data['order_by'] != '') ? $data['order_by'] : 'ASC';
            $channelCategoryId = (isset($data['category_id']) && $data['category_id'] != '') ? $data['category_id'] : 0;
            $fromDate = (isset($data['from_date']) && $data['from_date'] != '') ? $data['from_date'] : date('Y-m-d') . ' 00:00:00';
            $toDate = (isset($data['to_date']) && $data['to_date'] != '') ? $data['to_date'] : 'now()';
            $categoryId = 0;
            $channelId = DB::table('api_session')
                    ->where(array('api_key' => $data['api_key'], 'secret_key' => $data['secret_key']))
                    ->pluck('mp_id')->all();
            if (count($channelId) > 0)
                $channelId = $channelId[0];
            else
                $channelId = 0;
            if ($channelCategoryId > 0) {
                $categoryId = DB::Table('mp_category_mapping')
                        ->where(['mp_id' => $channelId, 'mp_category_id' => $channelCategoryId])
                        ->pluck('category_id')->all();
                if (count($categoryId) > 0)
                    $categoryId = $categoryId[0];
                else
                    $categoryId = 0;
            }
            $limit = 500;
            if ($start_limit == 0 && $end_limit > 0) {
                if ($end_limit > $limit) {
                    $start_limit = ($end_limit - $limit);
                } else {
                    $limit = $end_limit;
                }
            } elseif ($start_limit > 0 && $end_limit == 0) {
                //nothing to do this works fine
            }if ($start_limit > 0 && $end_limit > 0) {
                if ($end_limit > $start_limit) {
                    if (($end_limit - $start_limit) < $limit) {
                        $limit = ($end_limit - $start_limit);
                    }
                }
            }
            $manufacturer_id = $this->apiAccess->getManufacturerId($data);

            $productQuery = "select 
                products.product_id, products.product_title as name, products.sku, products.category_id, attribute_set_mapping.attribute_set_id, 
                mp_category_mapping.category_id, "
                    . "product_inventory.available_inventory from products "
                    . "left join `product_inventory` on `product_inventory`.`product_id` = `products`.`product_id` "
                    . "left join `mp_product_add_update` on `mp_product_add_update`.`product_id` = `products`.`product_id` "
                    . "JOIN `product_relations` ON `product_relations`.`parent_id` = mp_product_add_update.`product_id` "
                    . "left join `mp_category_mapping` on `mp_category_mapping`.`category_id` = `products`.`category_id`"
                    . "left join `product_attributes` on `product_attributes`.`product_id` = `products`.`category_id`"
                    . "left join `attribute_set_mapping` on `attribute_set_mapping`.`attribute_id` = `product_attributes`.`attribute_id`"
                    . "where (`products`.`is_gds_enabled` = 1 and `products`.`is_deleted` = 0 and `products`.`is_active` = 1) "
                    . "and `product_inventory`.`available_inventory` is not null and mp_product_add_update.mp_id = 5 and mp_product_add_update.is_deleted = 0 ";
            if ($categoryId > 0) {
                $productQuery = $productQuery . " and products.category_id = " . $categoryId;
            }
            if ($isChannel) {
                $productQuery = $productQuery . " and mp_product_add_update.mp_id = " . $channelId;
            }
            if (!empty($manufacturer_id) && !$isChannel) {
                $productQuery = $productQuery . " and products.legal_entity_id = " . $manufacturer_id;
            }
            if ($toDate == 'now()') {
                $productQuery = $productQuery . " AND products.created_at BETWEEN '" . $fromDate . "' AND " . $toDate . " 
  OR products.updated_at BETWEEN '" . $fromDate . "' AND " . $toDate . " ";
            } else {
                $productQuery = $productQuery . " AND products.created_at BETWEEN '" . $fromDate . "' AND '" . $toDate . "' 
  OR products.updated_at BETWEEN '" . $fromDate . "' AND '" . $toDate . "' ";
            }

            $productQuery = $productQuery . " group by `products`.`product_id` "
                    . "order by `products`.`product_id` " . $orderBy . ' '
                    . " limit " . $start_limit . ", " . $limit;
           // Log::info('productQuery');
            //Log::info($productQuery);


            $products = DB::select(DB::raw($productQuery));
            if (!empty($products)) {
                $count = count($products);
                foreach ($products as $key => $value) {
                    if (!empty($value->sku)) {
                        $categories = explode(',', $value->category_id);
                        $channel_value = DB::table('api_session')->where(array('api_key' => $data['api_key'], 'secret_key' => $data['secret_key']))->first();
                        $prodCatarr = DB::Table('mp_categories')
                                        ->join('mp_category_mapping', 'mp_category_mapping.mp_category_id', '=', 'mp_categories.mp_category_id')
                                        ->whereIn('mp_category_mapping.category_id', $categories)
                                        ->where('mp_categories.mp_id', $channel_value->channel_id)
                                        ->select('category_name', 'mp_category_id')->first();

                        $finalarr = array();
                        $varientAttributes = array();
                        $varientQuery = DB::Table('product_attributes')
                                ->leftJoin('attributes', 'attributes.attribute_id', '=', 'product_attributes.attribute_id')
                                ->leftJoin('attribute_set_mapping', 'attribute_set_mapping.attribute_id', '=', 'product_attributes.attribute_id')
                                ->where(array('product_attributes.product_id' => $value->product_id, 'attribute_set_mapping.is_varient' => 1, 'attribute_set_mapping.attribute_set_id' => $value->attribute_set_id))
                                ->pluck(DB::raw('group_concat(attributes.attribute_code) as attribute_code'))->all();
                        if (!empty($varientQuery)) {
                            $varientAttributes[] = $varientQuery;
                        }
                        $childData = $this->getChildProductDynamicInfo($value->sku);
           //             Log::info('childData');
             //           Log::info($childData);
                        $finalarr['categories'] = $prodCatarr;
//                        $finalarr['product_id'] = $value->product_id;
                        $finalarr['varient_fields'] = (empty($childData)) ? [] : (!empty($varientAttributes) ? implode(',', $varientAttributes) : '');
                        $finalarr['varients'] = (empty($childData)) ? [] : $childData;
                        $finaltempProducts[] = $finalarr;
                    }
                }
                $status = 1;
                $message = 'Data Successfully Retrieved.';
            } else {
                $status = 1;
                $message = 'Empty records.';
            }
        } catch (ErrorException $e) {
            $status = 0;
            $message = $e->getMessage();
            //Log::info($e->getTraceAsString());
        }
        return json_encode(Array('Status' => $status, 'Message' => $message, 'count' => $count, 'Data' => $finaltempProducts));
    }

    /**
     * 
     * @param type $data
     * @return type category_id,name,description,parent
     * @Description: This API request is used to get all the available categories.
     */
    public function getAllCategories($data) {
        $status = 0;
        try {
            //$manufacturer_id = (isset($data['manufacturer_id']) && $data['manufacturer_id'] != '') ? $data['manufacturer_id'] : '';
            $manufacturer_id = $this->apiAccess->getManufacturerId($data);

            $categories = DB::select("SELECT  c.category_id as category_id,  c.cat_name as name,  c.description as description,  p.cat_name  as parent 
                FROM categories c left join categories p
              on c.parent_id=p.category_id order by p.parent_id");

            $status = 1;
            $message = 'Data Successfully Retrieved.';
        } catch (Exception $e) {
            $status = 0;
            $message = $e->getMessage();
        }
        return Response::json(Array('Status' => $status, 'Message' => $message, 'Data' => $categories));
    }

    /**
     * 
     * @param type $data
     * @return type status,message,array(categoryname,brand_name)
     * @Description: This API request is used to get all the categories along with customers.
     */
    public function getAllCategoriesByCustomer($data) {
        $status = 0;
        try {
            $manufacturerId = $this->apiAccess->getManufacturerId($data);
            $customer_sub_groups = DB::table('customer_categories')
                    ->select('customer_categories.category_id', 'categories.cat_name as name')
                    ->leftJoin('categories', 'categories.category_id', '=', 'customer_categories.category_id')
                    ->where('customer_categories.customer_id', $manufacturerId)
                    ->get()->all();
            $catArr = array();
            foreach ($customer_sub_groups as $key1 => $value1) {
                $catArr[$value1->category_id] = $value1->name;
            }
            $finalCustCatArr[] = $catArr;

            $status = 1;
            $message = 'Data Successfully Retrieved.';
        } catch (Exception $e) {
            $status = 0;
            $message = $e->getMessage();
        }
        return Response::json(Array('Status' => $status, 'Message' => $message, 'Data' => $finalCustCatArr));
    }

    //public function updateSalesOrders($data, $order_data, $orderId)
    public function updateSalesOrders($data) {

        try {
            $order_data = json_decode($data['order_data']);

            $orderId = $order_data->order_id;

            //Log::info(__METHOD__);
            $mfgId = $this->apiAccess->getManufacturerId($data);
            //Log::info(' mfgId => ' . $mfgId);
            if ($mfgId == 0) {
                return Response::json(Array('Status' => false, 'Message' => 'Wrong data.'));
            }
            $erpIntegrationData = $this->apiAccess->getErpIntegration($mfgId);
            if (empty($erpIntegrationData)) {
                return Response::json(Array('Status' => false, 'Message' => 'Wrong data.'));
            }
            $erpIntegrationId = $erpIntegrationData->id;
            $this->_url = $erpIntegrationData->web_service_url;
            $this->_method = 'Z0046_ESEAL_UPDATE_SO_SRV';
            $this->_method_name = 'UPDATE_SO';
            $url = $this->_url . $this->_method . '/' . $this->_method_name;
            $this->_sap_api_repo = new Central\Repositories\SapApiRepo();
            $this->_return_type = 'xml';
            $this->_token = $erpIntegrationData->token;
            $id = 123;
            $username = $erpIntegrationData->web_service_username;
            $password = $erpIntegrationData->web_service_password;
            if (property_exists($order_data, 'channel_id')) {
                $channelId = $order_data->channel_id;
            } else {
                return Response::json(Array('Status' => false, 'Message' => 'Need channel id.'));
            }
            $erpIntegrationAdditionalData = $this->apiAccess->getErpIntegrationAdditionalData($erpIntegrationId, $channelId);

            $erpOrderID = DB::table('eseal_orders')->where('order_id', $orderId)->pluck('erp_order_id')->all();

            $SALES_ORG = $erpIntegrationAdditionalData->sales_org;
            $DISTR_CHAN = $erpIntegrationAdditionalData->distr_chan;
            $DIVISION = $erpIntegrationAdditionalData->division;
            $CREATE_DELIVERY = $erpIntegrationAdditionalData->create_delivery;
            $SHIPPING_POINT = $erpIntegrationAdditionalData->shipping_point;
            $DOC_TYPE = $erpIntegrationAdditionalData->doc_type;
            $SH_PARTN_NUMB = $erpIntegrationAdditionalData->sh_partn_numb;
            $SP_PARTN_NUMB = $erpIntegrationAdditionalData->sp_partn_numb;

            $indicator = $order_data->indicator;
            $itemData = '';
            $patnersData = '';
            $plantCode = 1010;
            //Log::info($order_data->products);
            foreach ($order_data->products as $product) {
                $productData = DB::table('products')
                        ->select('products.product_id', 'products.product_title as name', 'products.weight_class_id', 'products.material_code', 'eseal_customer.customer_id')
                        ->leftJoin('eseal_customer', 'eseal_customer.customer_id', '=', 'products.legal_entity_id')
                        ->where('products.sku', $product->sku)
                        ->first();
                if (!empty($productData)) {
                    $itemData = $itemData . '<ZESEALS046_SO_UPDATE MATERIAL="' . substr(str_replace(' ', '', $productData->material_code), 0, 18) . '" BATCH="" PLANT="' . $plantCode . '" STORE_LOC="" TARGET_QTY="' . $product->quantity . '" INDICATOR="' . $indicator . '" />';
                } else {
                    return Response::json(Array('Status' => false, 'Message' => 'Wrong Sku.'));
                }
            }
            $patnersData = $patnersData . '<PARTNER ROLE="SP" PARTN_NUMB="' . $SP_PARTN_NUMB . '" ITM_NUMBER="" TITLE="" NAME="" NAME_2="" NAME_3="" STREET="" COUNTRY_KEY="" POSTL_CODE="" CITY="" DISTRICT="" REGION_KEY="" TELEPHONE="" /> ';
            $esealKey = uniqid();
            $xml = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:m="http://schemas.microsoft.com/ado/2007/08/dataservices/metadata" xmlns:d="http://schemas.microsoft.com/ado/2007/08/dataservices" xml:base="http://14.141.81.243:8000/sap/opu/odata/sap/Z0046_ESEAL_UPDATE_SO_SRV/">
                <id>http://14.141.81.243:8000/sap/opu/odata/sap/Z0046_ESEAL_UPDATE_SO_SRV/UPDATE_SO(\'123\')</id>
                <title type="text">UPDATE_SO(\'123\')</title>
                <updated>2015-09-16T13:30:24Z</updated>
                <category term="Z0046_ESEAL_UPDATE_SO_SRV.UPDATE_SO" scheme="http://schemas.microsoft.com/ado/2007/08/dataservices/scheme" />
                <link href="UPDATE_SO(\'123\')" rel="self" title="UPDATE_SO" />
                <content type="application/xml">
                 <m:properties>
                <d:ESEAL_INPUT>"<![CDATA[ <?xml version="1.0" encoding="utf-8" ?> 
                  <REQUEST>
                  <DATA>
                  <INPUT TOKEN="' . $this->_token . '" ESEAL_KEY="' . $esealKey . '" SALES_ORDER="' . $erpOrderID . '" /> 
                  <SO_UPDATE>
                  ' . $itemData . '
                  </SO_UPDATE>
                  </DATA></REQUEST> ]]>"
                </d:ESEAL_INPUT>
                <d:ESEAL_OUTPUT />
                </m:properties>
                </content>
                </entry>';
            //echo $xml;
            //echo "-----------------------------";
            //Log::info($xml);
            //echo "<pre>";print_R($xml);die;
            $method = 'GET';
            $response = $this->_sap_api_repo->request($username, $password, $url, $method, null, 'xml', 2, '', $xml);
            //Log::info($response);
            //echo $response;
            $parseData1 = xml_parser_create();
            xml_parse_into_struct($parseData1, $response, $documentValues1, $documentIndex1);
            xml_parser_free($parseData1);

            $documentData = '';
            $status = '';
            foreach ($documentValues1 as $data) {
                if (isset($data['tag']) && $data['tag'] == 'D:ESEAL_OUTPUT') {
                    $documentData = $data['value'];
                }
            }

            return Response::json(Array('Status' => true, 'Message' => $status));
        } catch (\ErrorException $ex) {
            return $ex->getMessage() . $ex->getTraceAsString();
        }
    }

    /**
     * 
     * @param type $data,$order_id,$api_key,$secret_key
     * @return type Status,Message,orderId
     * @Description:This API call will cancel the order by updating the order status to 2.
     */
    public function cancelOrder($data) {
        try {
            $_orderModuleModel = new dmapiOrders();
            $status = 0;
            $message = '';
            $cancel_data = json_decode($data['orderdata'], true);
            if (!empty($cancel_data['customer_token'])) {
                $password_token = $cancel_data['customer_token'];
            }
            if (!empty($cancel_data['sales_token'])) {
                $password_token = $cancel_data['sales_token'];
            }
            if (!empty($password_token)) {
                $user_id = $_orderModuleModel->findUserIdByPassword($password_token);
            }
            Session::put('userId', $user_id);
            $_orderController = new OrdersController();
            $cancel_status_id = 17009;
            $comment = '';

            if (!empty($cancel_data)) {

                if (isset($cancel_data['order_id']) && isset($cancel_data['channel_order_id'])) {

                    if (!empty($cancel_data['order_id']) && !empty($cancel_data['channel_order_id'])) {
                        $orders = $_orderModuleModel->getOrderDetailById($cancel_data['order_id']);
                        if (isset($cancel_data['channel_order_id']) && !isset($cancel_data['channelId'])) {
                            $status = 0;
                            $message = 'ChannelId is required in this case';
                            return $this->returnJsonMessage($status, $message);
                        }

                        if (isset($orders->mp_id)) {
                            if ($orders->mp_id != $cancel_data['channelId']) {
                                $status = 400;
                                $message = " Channelid is invalid ...";
                                return $this->returnJsonMessage($status, $message);
                            }
                        }
                        if (isset($orders->mp_order_id)) {
                            if ($orders->mp_order_id != $cancel_data['channel_order_id']) {
                                $status = 400;
                                $message = " Channel Order id / Order id mismatch ...";
                                return $this->returnJsonMessage($status, $message);
                            }
                        }
                    } else {
                        $status = 400;
                        $message = " Channel Order id / Order id given as empty ...";
                        return $this->returnJsonMessage($status, $message);
                    }
                }

                $gdsOrderId = DB::table('gds_orders')->where('gds_order_id', $cancel_data['order_id'])->get()->all();


                if (count($gdsOrderId) > 0) {
                    $cancelledArr = $this->_orderModel->getCancelledQtyByOrderId($cancel_data['order_id']);
                    $products = $this->_orderModel->getProductByOrderId($cancel_data['order_id']);
                    //var_dump($products);exit;
                    $product_dump = array();
                    $ordquant = 0;
                    foreach ($products as $product) {
                        $product_dump[$product->product_id] = $product;
                        $ordquant+= (int) $product->qty;
                    }
                    $availableQtyArr = $this->_orderModel->getAvailableQty($products, $cancelledArr);
                    if (count($availableQtyArr) < 0) {
                        //return error
                        $error = 'No Products in the order';
                        return $this->returnJsonMessage(500, $error);
                    }

                    $error = array();
                    $productsArr = array();
                    foreach ($cancel_data['product_info'] as $product) {
                        $product_id = $product['product_id'];
                        $cancelQuantity = $product['quantity'];
                        $comment = $product['comments'];
                        if ($availableQtyArr[$product_id] >= $cancelQuantity) {
                            $temp['product_id'] = $product['product_id'];
                            $temp['qty'] = $product['quantity'];
                            $temp['cancel_reason_id'] = $product['cancel_reason_id'];
                            array_push($productsArr,$temp);

                        } else {
                            // put into error zone
                            $error[] = "productid $product_id cancellation quantity more than the ordered quantity";
                            //return $this->returnJsonMessage(500, $error);
                        }
                    }
                    if (count($error) == 0) {                 
                        $_orderController->cancelOrderItem($cancel_data['order_id'],$productsArr,$cancel_status_id, true);
                        $status = 1;
                        $message[] = "Order cancellation updated";
                        $message[] = $cancel_data['order_id'];
                        $args = array("ConsoleClass" => 'mail', 'arguments' => array('DmapiCancelOrderTemplate', $cancel_data['order_id']));
                        $token = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
                        // \Notifications::addNotification(['note_code' => 'ORD001', 'note_message' => 'Order #ORDID updated Successfully', 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['ORDID' => $cancel_data['order_id']]]);
                        
                        //added on !st nov @prasenjit using avinash system
                        /*$totOrderedQty = $this->_orderModel->getOrderedQtyByOrderId($cancel_data['order_id']);
                        $totCancelledQty = $this->_orderModel->getCancelledTotalQtyByOrderId($cancel_data['order_id']);
                        if($totOrderedQty == $totCancelledQty) {*/
                            $this->_orderModel->updateOrderStatusById($cancel_data['order_id'],$cancel_status_id);
                        //}
                        //reverting cashback when full cancel
                        if($cancel_status_id == 17009 || $cancel_status_id == 17015){
                            $_orderController->revertCashbackFromOrder($cancel_data['order_id'],$cancel_status_id,"Order Cancelled!");
                        }
                        $this->saveComment($cancel_data['order_id'], 'Cancel Status', array('comment'=>$comment, 'order_status_id'=>$cancel_status_id));
                        return $this->returnJsonMessage($status, $message);
                    } else {
                        //return error
                        $error[] = "Order cancellation is not done";
                        return $this->returnJsonMessage(500, $error);
                    }
                } else {
                    $status = 0;
                    $message = 'Order doesnot exist.';
                    return $this->returnJsonMessage($status, $message);
                }
            } else {
                $status = 0;
                $message = 'Format not correct';
                return $this->returnJsonMessage($status, $message);
            }
        } catch (\Exception $e) {
            $status = 0;
            $message = $e->getMessage() . $e->getTraceAsString();
            Log::info($e);
            //return $this->returnJsonMessage(500, $error);
        }
        //return $this->returnJsonMessage($status, $message);
        //return Response::json(Array('Status' => $status, 'Message' => $message, 'orderId' => $cancel_data['order_id']));
    }

    public function deleteSalesOrder($data) {
        try {
            $status = 0;
            $response = array();
            $this->ApiAccess = new MasterApiRepo();
            $mfgId = $this->ApiAccess->getManufacturerId($data);
            if (empty($mfgId)) {
                return Response::json(Array('Status' => false, 'Message' => 'Wrong data.'));
            }
            /* $erpIntegrationData = $this->apiAccess->getErpIntegration($mfgId);
              if (empty($erpIntegrationData))
              {
              return Response::json(Array('Status' => false, 'Message' => 'Wrong data.'));
              }
              $this->_url = $erpIntegrationData->web_service_url;
              $this->_method = 'Z0045_DELETE_SALES_ORDER_SRV';
              $this->_method_name = 'DELETE_SO';
              $url = $this->_url . $this->_method . '/' . $this->_method_name;
              $this->_sap_api_repo = new Central\Repositories\SapApiRepo();
              $this->_return_type = 'xml';
              $this->_token = $erpIntegrationData->token;
              $username = $erpIntegrationData->web_service_username;
              $password = $erpIntegrationData->web_service_password;
              $sapClient = $erpIntegrationData->sap_client; */

            $orderId = $data['order_id'];
            $eseal_order_id = DB::table('gds_orders')
                    ->where(array('gds_order_id' => $orderId))
                    ->pluck('gds_orders.erp_order_id')->all();
            //$last = DB::getQueryLog();
            //echo "<pre>";print_R(end($last));die;
            $last = DB::getQueryLog();
//            Log::info(end($last));
  //          Log::info('eseal_order_id');
    //        Log::info($eseal_order_id);
            $orderData['TOKEN'] = $this->_token;
            $orderData['SALES_ORDER'] = $eseal_order_id;
            $response = $this->_sap_api_repo->request($username, $password, $url, 'GET', $orderData, $this->_return_type, '', '', '', $sapClient);
            $status = 1;
            $message = 'Order deleted sucessfully.';
        } catch (ErrorException $e) {
            $message = $e->getMessage();
        }
        return json_encode(['Status' => $status, 'Message' => $message, 'Response' => $response]);
    }

    public function getInventoryCount($data) {
        try {
            $productCount = 0;
            $message = 'No Data';
            $status = 0;
            $productType = (isset($data['parents']) && $data['parents'] != '') ? $data['parents'] : 0;
            $channelCategoryId = (isset($data['category_id']) && $data['category_id'] != '') ? $data['category_id'] : 0;
            date_default_timezone_set('Asia/Kolkata');
            $fromDate = (isset($data['from_date']) && $data['from_date'] != '') ? $data['from_date'] : date('Y-m-d') . ' 00:00:00';
            $toDate = (isset($data['to_date']) && $data['to_date'] != '') ? $data['to_date'] : date('Y-m-d H:i:s');
            if (strtotime($fromDate) > strtotime($toDate)) {
                $fromDate = date('Y-m-d') . ' 00:00:00';
            }
            $categoryId = 0;
            $channelId = DB::table('api_session')
                    ->where(array('api_key' => $data['api_key'], 'secret_key' => $data['secret_key']))
                    ->pluck('channel_id')->all();
            if ($channelCategoryId > 0) {
                $categoryId = DB::Table('channel_categories')
                        ->where(['channel_id' => $channelId, 'channel_category_id' => $channelCategoryId])
                        ->pluck('ebutor_category_id')->all();
            }
//            $manufacturer_id = $this->apiAccess->getManufacturerId($data);
            $whareArray = array('channel_product_add_update.channel_id' => $channelId);
            if ($categoryId > 0) {
                $whareArray['products.category_id'] = $categoryId;
            }
            $productCount = DB::Table('channel_product_add_update')
                            ->join('product_super_link', 'product_super_link.parent_id', '=', 'channel_product_add_update.product_id')
                            ->join('products', 'products.product_id', '=', 'channel_product_add_update.product_id')
                            ->where($whareArray)
                            ->WhereBetween('products.date_added', [$fromDate, $toDate])
                            ->orWhereBetween('products.date_modified', [$fromDate, $toDate])
//                            ->whereOr('products.updated_at', 'between', $fromDate, $toDate)
                            ->groupBy('products.product_id')
                            ->select('channel_product_add_update.product_id')->get()->all();
            $last = DB::getQueryLog();
           // Log::info(end($last));
            if (!empty($productCount)) {
                $productCount = count($productCount);
                $status = 1;
                $message = 'Count Retrieved Successfully';
            }
        } catch (ErrorException $ex) {
            Log::info($ex->getMessage() . $ex->getTraceAsString());
        }
        return json_encode(['Status' => $status, 'Message' => $message, 'Count' => $productCount]);
    }

    /*
      This function is used to get the product count irrespective of category id.
      Params : api key, secret key and category id optional.
      Returns : Product count.
     */

    public function getProductCount($data) {
        try {
            $result = '';
            $status = '';
            $message = '';
            $countProducts = 0;
            $products = array();
            $manufacturer_id = $this->apiAccess->getManufacturerId($data);
            $category_id = Input::get('category_id');

            $channel_id = DB::table('api_session')
                    ->where(array('api_key' => $data['api_key'], 'secret_key' => $data['secret_key']))
                    ->pluck('mp_id')->all();
            if (count($channel_id) > 0)
                $channel_id = $channel_id[0];
            else
                $channel_id = "";
            if (empty($manufacturer_id)) {
                if (!empty($channel_id)) {
                    $products = DB::Table('mp_product_add_update')->where(array('mp_id' => $channel_id))->pluck('product_id')->all();
                }
                if (!empty($products)) {
                    $products = $products;
                }
            }
            /* retrieve product count based on manufacturer_id and category_id */
            if (!empty($category_id)) {
                $ebutor_cat_id = DB::table('mp_category_mapping')
                                ->where(array('mp_category_id' => $category_id, 'mp_id' => $channel_id))->pluck('category_id')->all();
                if (count($ebutor_cat_id) > 0)
                    $ebutor_cat_id = $ebutor_cat_id[0];
                else
                    $ebutor_cat_id = 0;

                $countProducts = DB::table('products')
                        ->select('product_id', 'category_id')
                        ->where(array('category_id' => $ebutor_cat_id, 'is_gds_enabled' => 1, 'is_deleted' => 0));

                if (!empty($manufacturer_id)) {
                    $countProducts = $countProducts->where('legal_entity_id', $manufacturer_id);
                } else {
                    $countProducts = $countProducts->whereIn('product_id', $products);
                }
                $countProducts = $countProducts->count();
                $last = DB::getQueryLog();
           //     Log::info(end($last));
            } else {
                $countProducts = DB::table('products')
                        ->select('product_id', 'category_id')
                        ->where(array('is_gds_enabled' => 1, 'is_deleted' => 0));
                if (!empty($manufacturer_id)) {
                    $countProducts = $countProducts->where(array('legal_entity_id' => $manufacturer_id));
                } else {
                    $countProducts = $countProducts->whereIn('product_id', $products);
                }
                $countProducts = $countProducts->count();
                $last = DB::getQueryLog();
                //Log::info(end($last));
            }
            if (!empty($countProducts)) {
                $status = 1;
                $result = $countProducts;
                $message = 'Count Retrieved Successfully';
            } else {
                $status = 0;
                $result = 0;
                $message = 'No count Retrieved';
            }
        } catch (ErrorException $ex) {
            $status = 0;
            $result = 0;
            $message = $ex->getMessage();
        }
        return Response::json(['Status' => $status, 'Message' => $message, 'Count' => $result]);
    }

    public function getZonebyPincode($pincode, $manufacturerId) {
        try {
            //$pincode = Input::get('pincode');
            $result = '';
            $status = '';
            $message = '';
            //$values = '';

            $locationPincodeData = DB::table('cities_pincodes')
                    ->leftJoin('countries', 'countries.country_id', '=', 'cities_pincodes.country_id')
                    //->join(DB::raw('zone', 'zone.name', '=', 'cities_pincodes.state'))
                    ->select('cities_pincodes.*', 'countries.name as Country')
                    ->where(array('cities_pincodes.PinCode' => $pincode))
                    ->first();
            $last = DB::getQueryLog();
            $locationId = 0;
            if (!empty($locationPincodeData)) {
                $cityId = $locationPincodeData->city_id;
                $locationId = DB::table('location_city_mapping')
                        ->where(array('cities' => $cityId, 'manufacturer_id' => $manufacturerId))
                        ->pluck('location_id')->all();
            }
            if (!empty($locationId)) {
                $status = 1;
                $result = $locationId;
                $message = 'Data retrieved';
            } else {
                $status = 0;
                $result = $locationId;
                $message = 'No data Retrieved';
            }
        } catch (Exception $ex) {
            $message = $ex->getMessage();
        }
        return json_encode(['Status' => $status, 'Message' => $message, 'location_id' => $result]);
    }

    public function channelcharges($channel_id, $productinfo, $order_id, $manufacturer_id) {

        try {

            $category_charge = DB::table('channel_charges as cc')
                    ->leftJoin('channel_service_type as cst', 'cst.service_type_id', '=', 'cc.service_type_id')
                    ->where(['cst.service_type_name' => 'PRODUCT_CATEGORY_LIST_FEE', 'cc.channel_id' => $channel_id])
                    //->orWhere(['cst.service_type_name'=>'MARKET_PLACE_SHIPPING_DELIVERY_FEE','cc.channel_id'=>$orderInfo->channelid])
                    ->get()->all();

            foreach ($category_charge as $value) {

                $percentage_charge = ($value->charges * $productinfo->total) / 100;
                // print_r($value->service_type_id);exit;

                DB::table('manf_charges')
                        ->insert([
                            'charges' => $percentage_charge,
                            'channel_charges_id' => $value->channel_charges_id,
                            'channel_id' => $channel_id,
                            'eseal_fee' => $value->eseal_fee,
                            'service_type_id' => $value->service_type_id,
                            'reference_id' => $order_id,
                            'created_date' => date('Y-m-d H:i:s'),
                            'currency_id' => 4,
                            'manf_id' => $manufacturer_id,
                ]);
            }
        } catch (ErrorException $ex) {
            //Log::info($ex->getMessage());
            return 0;
        }
    }

    /* public function channelShippingDetails($order_id)
      {
      try
      {
      DB::table('channel_orders_address')->insert([
      'channel_id' => $order_data->channelid,
      'order_id' => $order_id,
      'first_name' => $data['first_name'],
      'last_name' => $data['last_name'],
      'middle_name' => $data['middle_name'],
      'suffix' => isset($data['suffix']) ? $data['suffix'] : '',
      'company' => $data['company'],
      'address1' => $order_data->shippingaddress1,
      'address2' => $order_data->shippingaddress2,
      'address_type' => $data['address_type'],
      'city' => $order_data->city,
      'state' => $order_data->state,
      'country' => $order_data->country,
      'pincode' => $order_data->pincode,
      'phone' => $order_data->shippingphone,
      'mobile' => $data['mobile_no'],
      'email' => $order_data->shippingemail,
      'updated_date' => $order_data->updateddate
      ]);
      } catch (ErrorException $ex)
      {
      //Log::info($ex->getTraceAsString());
      return 0;
      }
      } */

    /**
     * [gdsInventoryCheck description]
     * @param  [type] $productid [description]
     * @param  [type] $le_wh_id  [description]
     * @param  [type] $quantity  [description]
     * @return [type]            [description]
     * @created Prasenjit Chowdhury
     */
    public function gdsInventoryCheck($productid, $le_wh_id, $quantity) {

        try {

            DB::enableQueryLog();
            $inventory = DB::table('vw_inventory_report')
                    ->where('product_id', $productid)
                    ->where('le_wh_id', $le_wh_id)
                    ->pluck('available_inventory')->all();
            // var_dump($inventory);
            // var_dump(DB::getQueryLog());
            if (count($inventory) > 0) {
                $inventory = $inventory[0];
            } else {
                $status = 0;
                //$message="No Available Inventory for product".$productid;
                return $status; //$this->returnJsonMessage($status, $message);
            }
            //inventorycheck=((int)$inventory->soh + (int) $inventory->atp-(int)$inventory->order_qty))
            if ((int) $quantity <= (int) $inventory) {
                //echo "inside here";
                // DB::table('inventory')->where('product_id', $productid)->update(array('order_qty' => $orderqty,
                //     'atp' => $remainqty));
                $status = 2;
                return 2;
            } else {
                $status = 1;
                //echo "inside status 2";
                //$message="Ordered qty is More Than Available qty for product".$productid."";               
                return $status; //$this->returnJsonMessage($status,$message); 
            }
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function gdsInventoryupdate($channel_item_id, $quantity, $productid, $le_wh_id, $activity = null) {
        try {
            $inventory = DB::table('vw_inventory_report')
                    ->where('product_id', $productid)
                    ->where('le_wh_id', $le_wh_id)
                    ->pluck('available_inventory')->all();
            if (count($inventory) > 0) {
                $inventory = $inventory[0];
            } else {
                $status = 0;
                //$message="No Available Inventory for product".$productid;
                return $status; //$this->returnJsonMessage($status, $message);
            }
            //inventorycheck=((int)$inventory->soh + (int) $inventory->atp-(int)$inventory->order_qty))
            if ((int) $quantity <= (int) $inventory) {
                echo 'here';
                die;
                // DB::table('inventory')->where('product_id', $productid)->update(array('order_qty' => $orderqty,
                //     'atp' => $remainqty));
                $status = 2;
                return 2;
            } else {
                $status = 1;
                //$message="Ordered qty is More Than Available qty for product".$productid."";               
                return $status; //$this->returnJsonMessage($status,$message); 
            }
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    /**
     * [gdsInventoryupdateFactail description]
     * @param  [type] $channel_item_id [For the factail the product id will be like the channel item id]
     * @return [type] $quantity []
     */
    public function gdsInventoryupdateFactail($channel_item_id, $quantity, $productid, $le_wh_id, $activty = null) {

        try {

            $mp_product_add_update = DB::table('mp_product_add_update as mp_product')
                            ->where('mp_product.product_id', $channel_item_id)
                            ->select('mp_product.mp_product_key')->get()->all();
            $product = $mp_product_add_update[0]->mp_product_key;
            if (!is_null($product) || $product != '') {
                //use the product else move on
            } else {
                //Update the product id with mp_product_key
                DB::table('mp_product_add_update')->where('product_id', $channel_item_id)->update(['mp_product_key' => $channel_item_id]);
            }
            $return = $this->gdsInventoryupdate($channel_item_id, $quantity, $productid, $le_wh_id);
            return $return;
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    /**
     * [updateChannelOrderIdFactail description]
     * @param  [type] $orderId [the newly genetated gds_order id]
     * @return [boolean]          [true/false]
     */
    public function updateChannelOrderIdFactail($orderId) {

        if (isset($orderId) && !is_null($orderId)) {
            $gdsOrder = new GDSOrders();
            $gdsOrder->where('gds_order_id', $orderId)->update(['mp_order_id' => $orderId]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * [productSearch description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function productSearch($data) {
        try {
            $productSearch = array();
            $productObj = new Products\Products();
            $category = isset($data['category_id']) ? $data['category_id'] : '';
            $productName = isset($data['product_name']) ? $data['product_name'] : '';
            $sku = isset($data['sku']) ? $data['sku'] : '';
            $brand_name = isset($data['brand_name']) ? $data['brand_name'] : '';
            $mrpl = isset($data['mrp-low']) ? $data['mrp-low'] : '';
            $mrph = isset($data['mrp-high']) ? $data['mrp-high'] : '';
            $start_limit = (isset($data['start_limit']) && $data['start_limit'] != '') ? $data['start_limit'] : 0;
            $end_limit = (isset($data['end_limit']) && $data['end_limit'] != '') ? $data['end_limit'] : 50;

            if (!empty($category)) {
                $productSearchQuery = "Select `products`.`name` as `product_name`, `products`.`title`, `products`.`category_id`, `products`.`sku`, `products`.`mrp`, `eseal_customer`.`brand_name` 
                    from `products` ";
                $productSearchQuery = $productSearchQuery . " join eseal_customer on eseal_customer.customer_id = products.legal_entity_id";
                $productSearchQuery = $productSearchQuery . " join categories on products.category_id = categories.category_id ";
                $productSearchWhere = " Where ";
                $where = 0;

                if ($category != '') {
                    $where = 1;
                    $productSearchWhere = $productSearchWhere . ' (categories.category_id LIKE "%' . $category . '%") ';
                }
                if ($productName != '') {
                    if ($where) {
                        $productSearchWhere = $productSearchWhere . ' AND (products.product_title LIKE "%' . $productName . '%") ';
                    } else {
                        $productSearchWhere = $productSearchWhere . ' (products.product_title LIKE "%' . $productName . '%") ';
                    }
                    $where = 1;
                }
                if ($sku != '') {
                    if ($where) {
                        $productSearchWhere = $productSearchWhere . ' AND (products.sku LIKE "%' . $sku . '%") ';
                    } else {
                        $productSearchWhere = $productSearchWhere . ' (products.sku LIKE "%' . $sku . '%") ';
                    }
                    $where = 1;
                }

                if ($brand_name != '') {
                    if ($where) {
                        $productSearchWhere = $productSearchWhere . ' AND (eseal_customer.brand_name LIKE "%' . $brand_name . '%") ';
                    } else {
                        $productSearchWhere = $productSearchWhere . ' (eseal_customer.brand_name LIKE "%' . $brand_name . '%") ';
                    }
                    $where = 1;
                }
                $limit = ' limit ' . $end_limit . ' offset ' . $start_limit;
                $completeQuery = $productSearchQuery . $productSearchWhere;
                $productSearch = DB::select(DB::raw($completeQuery));
            } else {
                $productSearch = DB::Table('products')
                        ->Join('categories', 'products.category_id', '=', 'categories.category_id')
                        ->Join('eseal_customer', 'eseal_customer.customer_id', '=', 'products.legal_entity_id')
                        ->select('products.product_title as product_name', 'products.title', 'products.category_id', 'products.sku', 'products.mrp', 'eseal_customer.brand_name')
                        //->where('products.category_id','like','%'.$category.'%')
                        ->where('products.product_title', 'like', '%' . $productName . '%')
                        //->whereRaw('MATCH(products.product_name) AGAINST("'.$productName.'" )')
                        ->orWhere('products.sku', 'like', '%' . $productName . '%')
                        ->orWhere('products.title', 'like', '%' . $productName . '%')
                        ->orWhere('eseal_customer.brand_name', 'like', '%' . $productName . '%')
                        ->skip($start_limit)->take($end_limit)
                        ->get()->all();
                //$last = DB::getQueryLog();
            }
            //return json_encode($productSearch);
            $status = 1;
            $message = 'Sucessfull';
        } catch (ErrorException $e) {
            //Log::info($e->getTraceAsString());
            $status = 0;
            $message = $e->getMessage();
            //return $message;
        }
        //return json_encode(Array('Status' => $status, 'Message' => $message, 'search_info' => $productSearch));
        return Response::json(Array('Status' => $status, 'Message' => $message, 'search_info' => $productSearch));
    }

    public function createGdsOrders() {
        $orderId = Input::get('order_id');
        $manufacturerId = Input::get('manufacturer_id');
        $this->gdsOrder(['order_id' => $orderId, 'manufacturer_id' => $manufacturerId]);
    }

    public function finance($data) {
        try {
            $status = 0;
            $message = '';

            $this->subscriptionCharges($data);
            $this->channelOrderCharges($data);
            $this->getCategoryCharges($data);
            $status = 1;
            $message = 'Success';
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return Response::json(Array('Status' => $status, 'Message' => $message));
    }

    public function subscriptionCharges($data) {
        try {
            $status = 0;
            $message = '';
            $payment_term = DB::table('channel_charges')
                    ->join('channel_service_type', 'channel_service_type.service_type_id', '=', 'channel_charges.service_type_id')
                    ->leftJoin('master_lookup', 'channel_charges.payment_term', '=', 'master_lookup.id')
                    ->leftJoin('lookup_categories', 'master_lookup.category_id', '=', 'lookup_categories.id')
                    ->where('lookup_categories.cat_name', "Recurring Period")
                    ->where('channel_service_type.service_type_id', '=', '1')
                    ->select(['master_lookup.name as payment_term'])
                    ->get()->all();

            if ($payment_term[0]->payment_term == 'Monthly') {

                $manf_channels = DB::table('channel_charges')
                        ->join('manf_channels', 'manf_channels.channel_id', '=', 'channel_charges.channel_id')
                        ->where('channel_charges.service_type_id', '=', 1)
                        ->where('manf_channels.status', '=', 1)
                        ->select(['channel_charges.charges', 'channel_charges.channel_charges_id', 'manf_channels.manf_channel_id as reference_id', 'channel_charges.eseal_fee', 'channel_charges.channel_id', 'channel_charges.service_type_id', 'channel_charges.currency_id'])
                        ->get()->all();


                $charges_entities = DB::table('charges_entities')
                        ->where('entity_table_name', '=', 'channel_charges')
                        ->select(['charges_entity_id as entity_table_name_id'])
                        ->get()->all();

                $array = array_merge((json_decode(json_encode($manf_channels), true)), (json_decode(json_encode($charges_entities), true)));

                $oneDimensionalArray = call_user_func_array('array_merge', $array);


                $this->manfCharges($oneDimensionalArray);
            }

            $status = 1;
            $message = 'Success.';
        } catch (ErrorException $ex) {
            $message = $ex->getMessage() . $ex->getTraceAsString();
            echo $message;
            die;
            //Log::info();
            //Log::info($ex->getMessage());
            //Log::info($ex->getTraceAsString());
            return 0;
        }
        return Response::json(Array('Status' => $status, 'Message' => $message));
    }

    public function getCategoryCharges($data) {
        try {
            $status = 0;
            $message = '';
            $product = $data['product_key'];       //110167581347

            $product_id = DB::table('Channel_product_add_update')
                    ->select(['Channel_product_add_update.product_id', 'Channel_product_add_update.channel_product_key as reference_id'])
                    ->where('Channel_product_add_update.channel_product_key', '=', $product)
                    ->get()->all();
            //print_r($product_id);die;
            $prodctg = DB::table('products')
                    ->select('products.category_id')
                    ->where('products.product_id', '=', $product_id[0]->product_id)
                    ->get()->all();

            //print_r($prodctg);die;      
            $category = DB::table('products')
                    ->join('categories', 'products.category_id', '=', 'categories.category_id')
                    ->whereIn('categories.category_id', [$prodctg[0]->category_id])
                    ->where('products.product_id', '=', $product_id[0]->product_id)
                    ->where('categories.parent_id', '=', 0)
                    ->select('categories.category_id')
                    ->get()->all();
            //print_r($category);die;    
            $ctgcharges = DB::table('category_charges')
                    ->select(['charges'])
                    ->where('category_id', '=', $category[0]->category_id)
                    ->get()->all();
            //print_r($ctgcharges[0]->charges);die;   
            $charges_entities = DB::table('charges_entities')
                    ->where('entity_table_name', '=', 'Channel_product_add_update')
                    ->select(['charges_entity_id as entity_table_name_id'])
                    ->get()->all();
            //return $charges_entities;
            $manf_data = db::table('channel_charges')
                    ->join('channel_service_type', 'channel_service_type.service_type_id', '=', 'channel_charges.service_type_id')
                    ->where('channel_service_type.service_type_name', '=', 'PRODUCT_CATEGORY_LIST_FEE')
                    ->select(['channel_charges.channel_charges_id', 'channel_charges.service_type_id', 'channel_charges.eseal_fee', 'channel_charges.channel_id', 'channel_charges.currency_id'])
                    ->get()->all();


            $array1 = array_merge((json_decode(json_encode($ctgcharges), true)), (json_decode(json_encode($manf_data), true)));
            //echo "<pre> aarray=>",print_r($array1);

            $array2 = array_merge((json_decode(json_encode($charges_entities), true)), (json_decode(json_encode($product_id), true)));
            //echo "<pre> aarray=>",print_r($array2);

            $array = array_merge((json_decode(json_encode($array1), true)), (json_decode(json_encode($array2), true)));
            //echo "<pre> aarray=>",print_r($array);die;

            $oneDimensionalArray = call_user_func_array('array_merge', $array);
            //echo "<pre> aarray=>",print_r($oneDimensionalArray);

            $this->manfCharges($oneDimensionalArray);
            $status = 1;
            $message = 'Successfully inserted  into manf_charges.';
        } catch (ErrorException $ex) {
            $message = $ex->getMessage() . $ex->getTraceAsString();
            echo $message;
            die;
            //Log::info();
            //Log::info($ex->getMessage());
            //Log::info($ex->getTraceAsString());
            return 0;
        }
        return Response::json(Array('Status' => $status, 'Message' => $message));
    }

    public function channelOrderCharges($data) {
        try {

            $status = 0;
            $message = '';

            $fromDate = $data['fromDate'];
            $toDate = $data['toDate'];


            $charge = DB::table('channel_charges')
                    ->leftJoin('channel_service_type as service', 'service.service_type_id', '=', 'channel_charges.service_type_id')
                    ->leftJoin('currency', 'currency.currency_id', '=', 'channel_charges.currency_id')
                    ->leftJoin('channel_orders as orders', 'orders.channel_id', '=', 'channel_charges.channel_id')
                    ->where('service.service_type_name', '=', 'PAYMENT_GATEWAY_FEE')
                    ->whereBetween('orders.created_date', [$fromDate, $toDate])
                    ->select('channel_charges.channel_charges_id', 'channel_charges.eseal_fee', 'channel_charges.charges as chrg', 'channel_charges.currency_id', 'channel_charges.service_type_id', 'orders.channel_order_id', 'orders.order_id as reference_id', 'orders.total_amount as charges', 'orders.channel_id', 'orders.created_date')
                    ->get()->all();
            /* echo "<pre>"; echo "Hello"; print_r($charge); die(); */
            if (!empty($charge)) {

                $charges_entities = DB::table('charges_entities')
                        ->where('entity_table_name', '=', 'channel_orders')
                        ->select(['charges_entity_id as entity_table_name_id'])
                        ->get()->all();
                $charge_id = isset($charges_entities[0]->entity_table_name_id) ? $charges_entities[0]->entity_table_name_id : '';


                $amount = array();

                foreach ($charge as $key => $value) {
                    $amount = $value->charges * $value->chrg;
                    $value->charges = $amount / 100;
                    $value->entity_table_name_id = $charge_id;
                }
                $order_charge = json_decode(json_encode($charge), true);

                foreach ($order_charge as $key => $value) {
                    /* echo "<pre>"; print_r($value); */

                    $manfId = DB::table('manf_charges')->where([
                                'reference_id' => $value['reference_id'],
                                'channel_id' => $value['channel_id']
                            ])->pluck('manf_charges_id')->all();
                    /* $last = DB::getQueryLog();
                      print_R(end($last)); */
                    //if (!empty($manfId)) {
                    if ($manfId != '') {

                        $message = 'ManfCharge already exists for referenceId: ' . $value['reference_id'];
                    } else {
                        $this->manfCharges($value);
                        $status = 1;
                        $message = 'Successfully inserted  into manf_charges.';
                    }
                }
            } else {
                $message = 'No Data Found in ChannelOrderCharges';
            }
        } catch (ErrorException $ex) {
            $message = $ex->getMessage() . $ex->getTraceAsString();

            return 0;
        }
        return Response::json(Array('Status' => $status, 'Message' => $message));
    }

    public function manfCharges($data) {

        try {
            $status = 0;
            $message = '';
            $manf_charges = DB::table('manf_charges')->insert([
                'charges' => $data['charges'],
                'channel_charges_id' => $data['channel_charges_id'],
                'reference_id' => $data['reference_id'],
                'eseal_fee' => $data['eseal_fee'],
                'channel_id' => $data['channel_id'],
                'service_type_id' => $data['service_type_id'],
                'entity_table_name_id' => $data['entity_table_name_id'],
                'currency_id' => $data['currency_id']
            ]);
        } catch (ErrorException $ex) {
            $message = $ex->getMessage() . $ex->getTraceAsString();
            echo $message;
            die;
            //Log::info();
            //Log::info($ex->getMessage());
            //Log::info($ex->getTraceAsString());
            return 0;
        }
        return Response::json(Array('Status' => $status, 'Message' => $message));
    }

    public function returnData($status, $message, $data) {
        return Response::json(Array('Status' => $status, 'Message' => $message, 'Data' => $data));
    }

    public function getMaterialCode($sku) {
        try {
            $status = 0;
            $data = 0;
            $message = '';
            $productData = DB::table('products')->where(['sku' => $sku])->pluck('material_code')->all();
            if ($productData != '') {
                $data = $productData;
                $status = 1;
                $message = 'Success';
            } else {
                $message = 'No product.';
            }
        } catch (ErrorException $ex) {
            $message = $ex->getMessage();
        }
        return json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }

    public function shippingDetails($data) {
        try {
            $status = 0;
            $message = '';
            $shipping_data = json_decode($data['shipping_data']);
            //echo "<pre/>";print_r($shipping_data);exit;
            DB::beginTransaction();

            $channel_order_id = $shipping_data->channel_shipping_details->channel_order_id;
            $chn_shipping_data = $shipping_data->channel_shipping_details;
            $gds_shipping_data = $shipping_data->gds_shipping_details;
            //echo "<pre/>";print_r($gds_shipping_data);exit;
            $channel_orders_shipped_detailArr = [
                'channel_order_id' => property_exists($chn_shipping_data, 'channel_order_id') ? $chn_shipping_data->channel_order_id : '',
                'ship_method' => property_exists($chn_shipping_data, 'ship_method') ? $chn_shipping_data->ship_method : '',
                'ship_service_id' => property_exists($chn_shipping_data, 'ship_service_id') ? $chn_shipping_data->ship_service_id : '',
                'tracking_id' => property_exists($chn_shipping_data, 'tracking_id') ? $chn_shipping_data->tracking_id : '',
                'created_date' => date('Y-m-d H:i:s'),
                'fname' => property_exists($chn_shipping_data, 'fname') ? $chn_shipping_data->fname : '',
                'mname' => property_exists($chn_shipping_data, 'mname') ? $chn_shipping_data->mname : '',
                'lname' => property_exists($chn_shipping_data, 'lname') ? $chn_shipping_data->lname : '',
                'addr1' => property_exists($chn_shipping_data, 'addr1') ? $chn_shipping_data->addr1 : '',
                'addr2' => property_exists($chn_shipping_data, 'addr2') ? $chn_shipping_data->addr2 : '',
                'city' => property_exists($chn_shipping_data, 'city') ? $chn_shipping_data->city : '',
                'postcode' => property_exists($chn_shipping_data, 'postcode') ? $chn_shipping_data->postcode : '',
                'country_id' => property_exists($chn_shipping_data, 'country_id') ? $chn_shipping_data->country_id : '',
                'state_id' => property_exists($chn_shipping_data, 'state_id') ? $chn_shipping_data->state_id : '',
                'telephone' => property_exists($chn_shipping_data, 'telephone') ? $chn_shipping_data->telephone : '',
                'mobile' => property_exists($chn_shipping_data, 'mobile') ? $chn_shipping_data->mobile : ''
            ];
            DB::table('channel_orders_shipped_dtl')->insert($channel_orders_shipped_detailArr);
            $channel_ship_id = DB::getPdo()->lastInsertId();
            //echo $channel_ship_id;exit;

            if (!empty($channel_ship_id)) {
                $channel_ship_itemsArr = [
                    'channel_order_id' => property_exists($channel_order_id, 'channel_order_id') ? $channel_order_id : '',
                    'channel_ship_id' => $channel_ship_id,
                    'pid' => property_exists($chn_shipping_data, 'pid') ? $chn_shipping_data->pid : '',
                    'qty' => property_exists($chn_shipping_data, 'qty') ? $chn_shipping_data->qty : '',
                    'created_date' => date('Y-m-d H:i:s'),
                    'item_status' => property_exists($chn_shipping_data, 'item_status') ? $chn_shipping_data->item_status : ''
                ];

                $channel_ship_item_id = DB::table('channel_ship_items')->insert($channel_ship_itemsArr);



                $gds_ship_gridArr = [
                    'gds_order_id' => property_exists($gds_shipping_data, 'gds_order_id') ? $gds_shipping_data->gds_order_id : '',
                    'created_date' => date('Y-m-d H:i:s')
                ];
                DB::table('gds_ship_grid')->insert($gds_ship_gridArr);
                $gds_ship_grid_id = DB::getPdo()->lastInsertId();

                $gds_orders_ship_detailsArr = [
                    'gds_order_id' => property_exists($gds_shipping_data, 'gds_order_id') ? $gds_shipping_data->gds_order_id : '',
                    'ship_method' => property_exists($chn_shipping_data, 'ship_method') ? $chn_shipping_data->ship_method : '',
                    'ship_service_id' => property_exists($chn_shipping_data, 'ship_service_id') ? $chn_shipping_data->ship_service_id : '',
                    'tracking_id' => property_exists($chn_shipping_data, 'tracking_id') ? $chn_shipping_data->tracking_id : '',
                    'created_date' => date('Y-m-d H:i:s'),
                    'fname' => property_exists($chn_shipping_data, 'fname') ? $chn_shipping_data->fname : '',
                    'mname' => property_exists($chn_shipping_data, 'mname') ? $chn_shipping_data->mname : '',
                    'lname' => property_exists($chn_shipping_data, 'lname') ? $chn_shipping_data->lname : '',
                    'addr1' => property_exists($chn_shipping_data, 'addr1') ? $chn_shipping_data->addr1 : '',
                    'addr2' => property_exists($chn_shipping_data, 'addr2') ? $chn_shipping_data->addr2 : '',
                    'city' => property_exists($chn_shipping_data, 'city') ? $chn_shipping_data->city : '',
                    'postcode' => property_exists($chn_shipping_data, 'postcode') ? $chn_shipping_data->postcode : '',
                    'country_id' => property_exists($chn_shipping_data, 'country_id') ? $chn_shipping_data->country_id : '',
                    'state_id' => property_exists($chn_shipping_data, 'state_id') ? $chn_shipping_data->state_id : '',
                    'telephone' => property_exists($chn_shipping_data, 'telephone') ? $chn_shipping_data->telephone : '',
                    'mobile' => property_exists($chn_shipping_data, 'mobile') ? $chn_shipping_data->mobile : ''
                ];
                DB::table('gds_orders_ship_details')->insert($gds_orders_ship_detailsArr);
                $order_ship_id = DB::getPdo()->lastInsertId();

                $gds_ship_itemsArr = [
                    'gds_order_id' => property_exists($gds_shipping_data, 'gds_order_id') ? $gds_shipping_data->gds_order_id : '',
                    'gds_ship_grid_id' => $gds_ship_grid_id,
                    'order_ship_id' => $order_ship_id,
                    'pid' => property_exists($chn_shipping_data, 'pid') ? $chn_shipping_data->pid : '',
                    'qty' => property_exists($chn_shipping_data, 'qty') ? $chn_shipping_data->qty : '',
                    'created_date' => date('Y-m-d H:i:s')
                ];
                DB::table('gds_ship_items')->insert($gds_ship_itemsArr);
                $gds_ship_item_id = DB::getPdo()->lastInsertId();
            }

            $status = 1;
            $message = "Successfully inserted the shipping details.";
            DB::commit();
        } catch (ErrorException $ex) {
            $message = $ex->getMessage();
            DB::rollback();
            return json_encode(['Status' => 0, 'Message' => $message]);
        }
        return json_encode(['Status' => $status, 'Message' => $message]);
    }

    public function updateShippingStatus($data) {

        try {
            $status = 0;
            $message = '';
            $status = $data['status'];
            $order_id = $data['order_id'];
            $gds_order_id = $data['gds_order_id'];

            //echo "<pre/>";print_r($data);exit;

            if (!empty($order_id) && !empty($gds_order_id) && !empty($status)) {

                DB::beginTransaction();
                DB::Table('channel_orders')->where('order_id', $order_id)->update(array('order_status' => $status));

                $status_id = DB::Table('channel_order_status')->where('status_value', $status)->pluck('status_id')->all();

                if (!empty($status_id)) {
                    DB::Table('gds_orders')->where('gds_order_id', $gds_order_id)->update(array('order_status_id' => $status_id));
                }
            } else {
                throw new Exception('Some of the parameters are missing.');
            }

            $status = 1;
            $message = "Successfully updated the order Status.";
            DB::commit();
        } catch (ErrorException $ex) {
            $message = $ex->getMessage();
            DB::rollback();
            return json_encode(['Status' => 0, 'Message' => $message]);
        }
        return json_encode(['Status' => $status, 'Message' => $message]);
    }

    public function getShippingDetails($data) {
        try {
            $status = 0;
            $message = '';
            $channel_ship_id = $data['channel_ship_id'];
            //echo "<pre/>";print_r($data);exit;
            $finalData = array();
            $tempArr = array();
            if (!empty($channel_ship_id)) {
                DB::beginTransaction();
                $channel_order_shipping_details = DB::Table('channel_orders_shipped_dtl')
                                ->leftJoin('channel_service_type', 'channel_service_type.service_type_id', '=', 'channel_orders_shipped_dtl.ship_service_id')
                                ->leftJoin('channel_ship_items', 'channel_ship_items.channel_ship_id', '=', 'channel_orders_shipped_dtl.channel_ship_id')
                                ->where('channel_orders_shipped_dtl.channel_ship_id', $channel_ship_id)
                                ->select('channel_orders_shipped_dtl.*', 'channel_service_type.service_type_name', 'channel_ship_items.pid', 'channel_ship_items.qty', 'channel_ship_items.item_status')->get()->all();
                //echo "<pre/>";print_r($channel_order_shipping_details);exit;
                /* foreach($channel_order_shipping_details as $key=>$val)
                  {
                  $tempArr['']
                  } */
                $finalData['channel_details'] = $channel_order_shipping_details;
            } else {
                throw new Exception('Some of the parameters are missing.');
            }

            $status = 1;
            $message = "Successfully retrieved the data.";
            DB::commit();
        } catch (ErrorException $ex) {
            $message = $ex->getMessage();
            DB::rollback();
            return json_encode(['Status' => 0, 'Message' => $message]);
        }
        return json_encode(['Status' => $status, 'Message' => $message, 'Data' => $finalData]);
    }

    /*
      This function is used to get the chahnnel related Products for publish to the market place.
      Author : Venkat Reddy Muthuru
      Date   : 17-March-2016.
      Params : api key, secret key and channel name.
      Returns : Product ids.
     */

    public function getChannelProducts($data) {
        try {
            $result = '';
            $status = '';
            $message = '';

            $channel_id = DB::table('channel')->where('channnel_name', $data['channel_name'])->pluck('channel_id')->all();

            $result = DB::table('channel_product_add_update')
                    ->leftJoin('products as prod', 'channel_product_add_update.product_id', '=', 'prod.product_id')
                    ->leftJoin('product_inventory', 'product_inventory.product_id', '=', 'channel_product_add_update.product_id')
                    ->leftJoin('locations', 'product_inventory.location_id', '=', 'locations.location_id')
                    ->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'prod.product_id')
                    ->leftJoin('attributes', 'product_attributes.attribute_id', '=', 'attributes.attribute_id')
                    ->leftJoin('channel_categories', 'channel_categories.ebutor_category_id', '=', 'channel_product_add_update.category_id')
                    ->where('is_added', 1)
                    ->where('channel_product_add_update.channel_id', $channel_id)
                    ->where('channel_categories.channel_id', $channel_id)
                    ->select('prod.product_id')
                    ->groupBy('prod.product_id')
                    //->take(5)
                    ->get()->all();
            $qr = DB::getQueryLog();
        } catch (ErrorException $ex) {
            $status = 0;
            $result = 0;
            $message = $ex->getMessage();
        }
        return Response::json(['Status' => $status, 'Message' => $message, 'Result' => $result]);
    }

    /*
      This function is used to get the seller information.
      Author : Venkat Reddy Muthuru
      Date   : 18-March-2016.
      Params : api key, secret key and channel name and product_id is optional.
      Returns : Seller Information.
     */

    public function getSellerInformation($data) {
        try {
            $result = '';
            $status = '';
            $message = '';

            $channel_id = DB::table('channel')->where('channnel_name', $data['channel_name'])->pluck('channel_id')->all();

            $result = DB::table('product_seller_mapping as ps')
                            ->leftJoin('ebutor_seller as es', 'es.seller_id', '=', 'ps.seller_id')
                            ->leftJoin('channel_configuration as cc', 'cc.seller_id', '=', 'ps.seller_id')
                            ->select('cc.Key_name', 'cc.Key_value')->where(array('ps.product_id' => $data['product_id']))->get()->all();

            $qr = DB::getQueryLog();
            //return end($qr);
        } catch (ErrorException $ex) {
            $status = 0;
            $result = 0;
            $message = $ex->getMessage();
        }
        return Response::json(['Status' => $status, 'Message' => $message, 'Result' => $result]);
    }

    public function getSlabRates($productId, $mrp, $marginPercentage, $dealerUnitPrice) {
        try {
            $tempSlab = array();
            $slabRates = DB::table('products_slab_rates')
                    ->select('products_slab_rates.start_range', 'products_slab_rates.end_range', 'products_slab_rates.price')
                    ->leftJoin('products', 'products.product_id', '=', 'products_slab_rates.product_id')
                    ->where('products_slab_rates.product_id', $productId)
                    ->get()->all();
            foreach ($slabRates as $slab) {
                $slabarr = array();
                $slabarr['pack'] = $slab->end_range;
//                $slabarr['margin_percentage'] = $marginPercentage;
                if ($mrp > 0) {
//                    $marginPer = (($mrp - $slab->price)/$mrp) * 100;
                    $marginPer = (($mrp - $slab->price) / $slab->price) * 100;
                } else {
                    $marginPer = $slab->price;
                }
                $slabarr['margin_percentage'] = round($marginPer, 2);
//                $slabarr['price'] = ($slab->price * 100) / (100 + $marginPercentage);
                $slabarr['price'] = ($slab->end_range * $slab->price);
//                $slabarr['unit_price'] = number_format((round(($slab->price * 100) / (100 + $marginPercentage), 2))/$slab->end_range, 2, '.', '');
                $slabarr['unit_price'] = $slab->price;
//                $slabarr['unit_price'] = $dealerUnitPrice;
                $tempSlab[] = $slabarr;
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return $tempSlab;
    }

    public function getBrandDetails($productId) {
        try {
            $brandDetails = [];
            if ($productId > 0) {
                $query = "SELECT manf_brands.id as brand_id, manf_brands.brand_name as value, 
                   if(manf_brands.brand_img is NULL,eseal_customer.logo,manf_brands.brand_img) as logo                            
                            FROM product_attributes 
                    JOIN manf_brands ON manf_brands.id = product_attributes.value
                    JOIN eseal_customer ON eseal_customer.customer_id = manf_brands.manf_id
                    WHERE product_attributes.product_id = " . $productId . "
                    AND product_attributes.attribute_id = 8 limit 1";
                $productBrandDetails = DB::select(DB::raw($query));
                if (empty($productBrandDetails)) {
                    $query = "SELECT '' as brand_id, product_attributes.value as value, eseal_customer.logo as logo 
                        FROM product_attributes 
                        join products on products.product_id = product_attributes.product_id
                        JOIN eseal_customer ON eseal_customer.customer_id = products.legal_entity_id
                        WHERE product_attributes.product_id = " . $productId . "
                        AND product_attributes.attribute_id = 8 limit 1";
                    $productBrandDetails = DB::select(DB::raw($query));
                }
                if (!empty($productBrandDetails)) {
                    foreach ($productBrandDetails as $brand) {
                        $brandLogo = property_exists($brand, 'logo') ? $brand->logo : '';
                        if ($brandLogo != '') {
                            if (strpos($brandLogo, 'www') !== false || strpos($brandLogo, 'http') !== false) {
                                $brand->logo = $brandLogo;
                            } else {
                                $brand->logo = URL::to('/') . '/uploads/customers/' . $brand->logo;
                            }
                        }
                    }
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return json_decode(json_encode($productBrandDetails));
    }

    public function getBrandValue($brandId) {
        try {
            $brandName = [];
            if ($brandId > 0) {
                $brandName = DB::table('manf_brands')
                        ->where('id', $brandId)
                        ->first(['brand_name']);
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return $brandName;
    }

    public function sendMails($ordersArray) {
        try {
            //Log::info($ordersArray);
            if (!empty($ordersArray)) {
                if (!is_array($ordersArray)) {
                    //Log::info($ordersArray);
                    if ($ordersArray > 0) {
                        $this->orderRepo->sendOrderEmail($ordersArray);
                    }
                } else {
                    foreach ($ordersArray as $orderId) {
                       // Log::info($orderId);
                        if ($orderId > 0) {
                            $this->orderRepo->sendOrderEmail($orderId);
                        }
                    }
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }

    /**
     * [testJob description]
     * @return [type] [description]
     */
    public function testJob($data) {

        $data = json_decode($data['orderdata']);
        if ($data) {
            $token = $data->token;
            $MongoDmapiModel = new MongoDmapiModel();
            $token = $MongoDmapiModel->getDmapiResponse($token);
            if (count($token) > 0) {

                //$status = 200;
                $message_parts = array();
                $message = $token[0]->responseData;
                if ($message === 'Not Yet Processeed') {
                    $message_parts['Status'] = 0;
                    $message_parts['Message'] = $message;
                    $message = $message_parts;
                } else {
                    $message = json_decode($message);
                }
                //return $this->returnJsonMessage($status, $message);
                return Response::json($message);
            } else {
                return Response::json(['Status' => 400, 'Message' => 'Token Not Found']);
            }
        } else {
            return Response::json(['Status' => 500, 'Message' => 'token validation error']);
        }
    }

    /**
     * [placeOrderBreakDown description]
     * @param  [type] $data [json]
     * @return [array] [ data with similar type of array ]
     */
    public function placeOrderBreakDown($data) {

        $functional_Data = json_decode($data, true);
        $functional_orderSubParts = json_decode($functional_Data['orderdata'], true);
        $functional_orderProducts = $functional_orderSubParts['product_info'];
        //clearing the product array from the data
        unset($functional_orderSubParts['product_info']);

        //segregrating orders on basis of warehouse 
        $warehouse_lists = array();
        foreach ($functional_orderProducts as $product) {
            $warehouse_lists[$product['le_wh_id']][] = $product;
        }

        if (count($warehouse_lists) > 0) {
            $split_orders = array();
            $count = 0; //for array merory allocation we will avoid on fly memory alloacation
            foreach ($warehouse_lists as $value) {
                $split_orders[$count]['api_key'] = $functional_Data['api_key'];
                $split_orders[$count]['secret_key'] = $functional_Data['secret_key'];
                $split_orders[$count]['orderdata'] = $functional_orderSubParts;
                $split_orders[$count]['orderdata']['product_info'] = $value;
                $json_encrpt = json_encode($split_orders[$count]['orderdata']);
                unset($split_orders[$count]['orderdata']);
                $split_orders[$count]['orderdata'] = $json_encrpt;
                $count++;
            }

            return $split_orders;
        } else {
            return false;
        }
    }

    /**
     * [placeOrder Earlier it was called from a base end this time will call everything as a queue]
     * @param  [type] $orderdata [description]
     * @return [type]            [description] //demo 
     */
    public function placeOrder($orderdata) {
        $MongoDmapiModel = new MongoDmapiModel();
        $data = json_encode($orderdata);

        try {
            @$ebutorChannelId = Config('dmapi.channelid');
            if ($ebutorChannelId == $this->channelId) {

                $data = json_decode($data['orderdata']);
                $customer_token = isset($data->additional_info->customer_token) ? $data->additional_info->customer_token : null;
                $dmapiOrders = new dmapiOrders();

                if (!is_null($customer_token) || !empty($customer_token)) {

                    $dmapiOrders->updateCart($customer_token, 0);
                }
            }
        } catch (Exception $e) {
            echo "Mail or email send failed";
        }
        //break the order here on basis of data
        $orderSplit = $this->placeOrderBreakDown($data);
        if (!$orderSplit) {
            return Response::json(['Status' => 1, 'Message' => 'Your Order is placed you will recieve the updated order details soon', 'transactionId' => $token, 'token' => $token_job]);
        } else {

            $token_array = array();
            $token_job_array = array();
            foreach ($orderSplit as $order) {

                $token = $MongoDmapiModel->insertDmapiRequest('placeOrder', $order);
                $data = json_encode($order);
                $data = base64_encode($data);
                $args = array("ConsoleClass" => 'dmapi', 'arguments' => array('placeOrder', $data, $token));
                $token_job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
                array_push($token_array, $token);
                array_push($token_job_array, $token_job);
            }

            return Response::json(['Status' => 1, 'Message' => 'Your Order is placed you will recieve the updated order details soon', 'transactionId' => implode(',', $token_array), 'token' => implode(',', $token_job_array)]);
        }
    }

    /**
     * placeOrder to be called from outside to DMAPI Generating from console
     * @param  [type] $orderdata [array]
     * @return [json]            [place order details]
     */
    public function placeOrderConsole($orderdata) {
        $warehouse_products = array(); //store the product Id warehouse key against each product
        try {
            //Log::info($orderdata);
            $ordersArray = [];
            $status = 0;
            $order_id = 0;
            $message = '';
            $orderDaTa = $orderdata['orderdata'];
            if (empty($orderDaTa)) {
                $status = 0;
                $message = 'Request Format not correct.';
                return $this->returnJsonMessage($status, $message);
            }
            $order_data = json_decode($orderdata['orderdata']);
            $customerData = array();
            $customerArray = isset($order_data->customer_info) ? ($order_data->customer_info) : array();
            if (empty($customerArray)) {
                $status = 0;
                $message = 'No Customer information.';
                return $this->returnJsonMessage($status, $message);
            }
            if (!empty($customerArray)) {
                $gdsCustomer = new gdsCustomer();
                $customerData = $gdsCustomer->gdsCustomer($customerArray);
            }
            //Log::info('customerData');
            //Log::info($customerData);

            $customerInfo = json_decode($customerData);

            if (isset($customerInfo->Status) && $customerInfo->Status == 0) {
                $status = 0;
                $message = isset($customerInfo->Status) ? $customerInfo->Status : 'Wrong data provided.';
                return $this->returnJsonMessage($status, $message);
            }
            $customer_id = isset($customerInfo->channel_cust_id) ? $customerInfo->channel_cust_id : 0;
            //Log::info('customer_id');
            //Log::info($customer_id);
            $channelId = $order_data->customer_info->channel_id;
            $this->channelId = $channelId;
            if (property_exists($order_data, 'order_info')) {
                $orderInfo = $order_data->order_info;
            }

            $orderInfo_array = get_object_vars($orderInfo);

           // Log::info('orderInfo');
            //Log::info($orderInfo_array);

            if (empty($orderInfo)) {
                $status = 0;
                $message = 'No Order info.';
                return $this->returnJsonMessage($status, $message);
            }

            $shipping_pincode = NULL;
            //check for address info will be requiring it for inventory check
            if (property_exists($order_data, 'address_info')) {

                if (empty($order_data->address_info)) {
                    $status = 0;
                    $message = 'Address missing for order.';
                    return $this->returnJsonMessage($status, $message);
                } else {

                    foreach ($order_data->address_info as $address) {

                        if ($address->address_type == 'shipping') {

                            $shipping_pincode = $address->pincode;
                        }
                    }
                }
            }

            $productDetails = array();
            $error_count = false;
            if (property_exists($order_data, 'product_info')) {

                if (empty($order_data->product_info)) {
                    $status = 0;
                    $message = 'Please Provide Product Info.';
                    return $this->returnJsonMessage($status, $message);
                } else {

                    $products_error = array();
                    foreach ($order_data->product_info as $products_array) {
                        if (empty($products_array->sku)) {
                            $status = 0;
                            $message = 'Please Provide Product Sku.';
                            return $this->returnJsonMessage($status, $message);
                        } else {

                            if (is_null($shipping_pincode)) {

                                $status = 0;
                                $message = 'Shipping pincode is missing cannot determine Warehouse';
                                return $this->returnJsonMessage($status, $message);
                            } else {
                                $this->le_warehouse_id = $products_array->le_wh_id;
                                $this->le_warehouses[$products_array->scoitemid] = $products_array->le_wh_id;
                                //checking for inventory before placing it inside insert
                            /*  
                                $product_id = $products_array->scoitemid;
                                $pincode = $shipping_pincode;
                                $quantity = $products_array->quantity;
                                $le_wh_id = $this->_orderModel->getWarehouseId($product_id, $pincode);
                                if ($le_wh_id) {
                                    $warehousestatus = $this->gdsInventoryCheck($product_id, $le_wh_id, $quantity);

                                    if ($warehousestatus == 0) {
                                        $message = "No Available Inventory for product " . $product_id;
                                        array_push($products_error, $message);
                                    }
                                    if ($warehousestatus == 1) {
                                        $message = "Ordered qty is More Than Available qty for product " . $product_id . "";
                                        array_push($products_error, $message);
                                    }if ($warehousestatus == 2) {
                                        $this->le_warehouses[$product_id] = $le_wh_id;
                                    } 
                                } else {
                                    $status = 0;
                                    $message = 'Product Id ' . $product_id . ' Not Serviceble for pincode ' . $pincode;
                                    //return $this->returnJsonMessage($status, $message);
                                    array_push($products_error, $message);
                                }*/

                            }
                        }
                    }

                    if (count($products_error) > 0) {
                        $status = 0;
                        $message = $products_error;
                        return $this->returnJsonMessage($status, $message);
                    }

                    $productDetails = $order_data->product_info;
                }
            }

           // Log::info('productDetails');
            //Log::info($productDetails);

            $ordersArray = array();
            $ordersIdActual = array();
            if (!empty($productDetails)) {

                $products = new Products();
                $manufacturer_id = $products->getManufactureId($productDetails);
            }
            if (!isset($manufacturer_id)) {
                $status = 0;
                $message = 'ManufacturerId is not found for the products';
                return $this->returnJsonMessage($status, $message);
            }
           // Log::info('manufacturer_id');
            //Log::info($manufacturer_id);
            $order_ids = $this->gdsOrderDetails($orderdata);
            if ($order_ids != 0) {
                if (empty($order_ids)) {
                    $status = 0;
                    $message = 'Unable to save order info.SKU not available';
                    return $this->returnJsonMessage($status, $message);
                }

              //  Log::info('order Ids');
                //Log::info(print_r($order_id, true));
                foreach ($order_ids as $order_id) { //for every order insert related info
                    if (!$order_id) {
                        $status = 0;
                        $message = 'Unable to save order info.';
                        return $this->returnJsonMessage($status, $message);
                    } else {
                        if ($customer_id) {
                            $customerAddress = isset($order_data->address_info) ? $order_data->address_info : array();
                        //    Log::info('customerAddress');
                          //  Log::info($customerAddress);
                            if (!empty($customerAddress)) {
                                $otherInfo['customer_id'] = $customer_id;
                                $otherInfo['gds_order_id'] = $order_id;
                                foreach ($customerAddress as $address) {
                                    $cust = $this->customerAddress($address, json_encode($otherInfo));
                                }
                            }
                        }
                    }
//                    Log::info('order_id');
  //                  Log::info($order_id);
                    if (!empty($order_id) && isset($order_id)) {
                        if (property_exists($order_data, 'payment_info')) {
                            $paymentDetails = $order_data->payment_info;
                        }
                        if (!empty($paymentDetails)) {
                            foreach ($paymentDetails as $paymentInfo) {
                                $this->gdschannelPayment($order_id, $paymentInfo);
                            }
                        }
                    }

                    $tempData['order_id'] = $order_id;
                    $tempData['manufacturer_id'] = $manufacturer_id;
                    $taxInfo = $this->getTaxDetails($order_id);
                    
                    $state_id = $this->order_dump[$order_id]['state'];
                    $_DmapiorderModel = new dmapiOrders();
                    $orderReference = $_DmapiorderModel->orderReference($state_id, 'SO');
                    //update gdstable tax
                    $taxupdate = DB::table('gds_orders')->where('gds_order_id', $order_id)
                            ->update(['tax_total' => $taxInfo, 'order_code' => $orderReference]);
                    /*
                      // Commented For Time Being
                      $erpIntegrationData = $this->apiAccess->getErpIntegration($manufacturer_id);
                      Log::info('erpIntegrationData');
                      Log::info($erpIntegrationData);
                      if (!empty($erpIntegrationData))
                      {
                      $this->createSalesOrders($orderdata, $productDetails, $order_id, $channelId);
                      } */

                    $status = 1;
                    $message = 'Successfully placed order.';
                    $args = array("ConsoleClass" => 'mail', 'arguments' => array('DmapiOrderTemplate', $order_id));
                    $token = $this->queue->enqueue('default', 'ResqueJobRiver', $args);

                    \Notifications::addNotification(['note_code' => 'ORD001', 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['ORDID' => $orderReference]]);

                    array_push($ordersArray, $orderReference);
                    array_push($ordersIdActual,$order_id);
                }
                $ordersArray = implode(',', $ordersArray);
                $ordersIdActual = implode(',',$ordersIdActual);
                //$this->sendMails($ordersArray);
            } else {
                //        DB::table('gds_orders');
                $status = 0;
                $message = 'No Available inventory for product.Hence Order Unsuccessful';
                return $this->returnJsonMessage($status, $message);
            }
        } catch (ErrorException $e) {
            $order_id = 0;
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            $message = 'Unable to save order please contact admin.';
        }
        return Response::json(array('Status' => $status, 'Message' => $message, 'order_id' => $ordersArray,'order_id_actual' => $ordersIdActual, 'customerLeId' => $this->customerLeId));
    }

    public function gdsCustomer($data) {
        try {
            if (is_array($data)) {
                $data = json_decode(json_encode($data), FALSE);
            }

            $status = 0;
            $message = '';
            $userId = 0;
            if (!property_exists($data, 'channel_user_id')) {
                $userId = DB::table('gds_customer')
                        ->where(['email_address' => $data->email_address, 'mp_id' => $data->channel_id])
                        ->pluck('gds_cust_id')->all();
                if (count($userId) > 0)
                    $userId = $userId[0];
                else
                    $userId = 0;
            }elseif (property_exists($data, 'channel_user_id') && $data->channel_user_id != '') {
                $userId = DB::table('gds_customer')
                        ->where(['mp_user_id' => $data->channel_user_id, 'mp_id' => $data->channel_id])
                        ->pluck('gds_cust_id')->all();
                if (count($userId) > 0)
                    $userId = $userId[0];
                else
                    $userId = 0;
            }elseif (property_exists($data, 'channel_user_id') && $data->channel_user_id == '') {
                $userId = DB::table('gds_customer')
                        ->where(['email_address' => $data->email_address, 'mp_id' => $data->channel_id])
                        ->pluck('gds_cust_id')->all();
                if (count($userId) > 0)
                    $userId = $userId[0];
                else
                    $userId = 0;
            }
            $customerArray = ['suffix' => isset($data->suffix) ? $data->suffix : '',
                'firstname' => $data->first_name,
                'lastname' => $data->last_name,
                'middlename' => $data->middle_name,
                'mp_user_id' => property_exists($data, 'channel_user_id') ? $data->channel_user_id : $data->email_address,
                'email_address' => $data->email_address,
                'mobile_no' => $data->mobile_no,
                'dob' => isset($data->dob) ? $data->dob : '',
                'mp_id' => $data->channel_id,
                'gender' => isset($data->gender) ? $data->gender : '',
                'registered_date' => isset($data->registered_date) ? $data->registered_date : date('Y-m-d H:i:s')
            ];


            try {
                if (empty($userId)) {
                    $userId = DB::table('gds_customer')->insertGetId($customerArray);
                    $status = 1;
                    $message = 'Successfully inserted.';
                } else {
                    if (isset($customerArray['mp_user_id'])) {
                        unset($customerArray['mp_user_id']);
                    }
                    if (isset($customerArray['mp_id'])) {
                        unset($customerArray['mp_id']);
                    }
                    if (isset($customerArray['registered_date'])) {
                        unset($customerArray['registered_date']);
                    }
                    DB::table('gds_customer')
                            ->where('gds_cust_id', $userId)
                            ->update($customerArray);
                    $status = 1;
                    $message = 'Successfully Updated.';
                }
               // Log::info('userId');
                //Log::info($userId);
                if ($userId > 0) {
                    $address1 = property_exists($data, 'address1') ? $data->address1 : '';
                   // Log::info('address1');
                    //Log::info($address1);
                    if ($address1 != '') {
                        $otherInfo['customer_id'] = $userId;
                        $otherInfo['order_id'] = 0;
                        $otherInfo['channel_id'] = $data->channel_id;
                      //  Log::info('otherInfo');
                      //  Log::info($otherInfo);
                        $data->email = $data->email_address;
                        $data->phone = '';
                        $response = $this->customerAddress($data, $otherInfo);
                        //Log::info('response');
                        //Log::info($response);
                    }
                }
            } catch (ErrorException $e) {
                $message = $e->getMessage();
            }
        } catch (Exception $e) {
            $message = $e->getMessage() . $e->getTraceAsString();
        }
        return json_encode(Array('Status' => $status, 'Message' => $message, 'channel_cust_id' => $userId));
    }

    public function customerAddress($data, $otherInfo) {
        try {
            $otherInfo = json_decode($otherInfo);
            $channelCustId = isset($otherInfo->customer_id) ? $otherInfo->customer_id : 0;
            $orderId = isset($otherInfo->gds_order_id) ? $otherInfo->gds_order_id : 0;

            if ($channelCustId) {
                if (isset($otherInfo->customer_id)) {
                    unset($otherInfo->customer_id);
                }
                $custAddressId = DB::table('gds_orders_addresses')
                                ->where(array('address_type' => $data->address_type,
                                    'gds_order_id' => $otherInfo->gds_order_id))->pluck('gds_addr_id')->all();

                if (count($custAddressId) > 0)
                    $custAddressId = $custAddressId[0];
                else
                    $custAddressId = "";
                $state_id = DB::table('zone')->where('name', $data->state)->pluck('zone_id')->all();
                if (count($state_id) > 0)
                    $state_id = $state_id[0];
                else
                    $state_id = 0;
                $country_id = DB::table('countries')->where('name', $data->country)->pluck('country_id')->all();
                if (count($country_id) > 0)
                    $country_id = $country_id[0];
                else
                    $country_id = 0;

                $custAddressArray = [
                    'fname' => property_exists($data, 'first_name') ? $data->first_name : '',
                    'mname' => property_exists($data, 'middle_name') ? $data->middle_name : '',
                    'lname' => property_exists($data, 'last_name') ? $data->last_name : '',
                    'address_type' => property_exists($data, 'address_type') ? $data->address_type : '',
                    'company' => property_exists($data, 'company') ? $data->company : '',
                    'addr1' => property_exists($data, 'address1') ? $data->address1 : '',
                    'addr2' => property_exists($data, 'address2') ? $data->address2 : '',
                    'city' => property_exists($data, 'city') ? $data->city : '',
                    'state_id' => $state_id,
                    'country_id' => $country_id,
                    'postcode' => property_exists($data, 'pincode') ? $data->pincode : '',
                    'telephone' => property_exists($data, 'phone') ? $data->phone : '',
                    'mobile' => property_exists($data, 'mobile_no') ? $data->mobile_no : '',
                    'gds_order_id' => $orderId,
                    'area' => property_exists($data, 'area_id') ? $data->area_id : '',
                    'landmark' => property_exists($data, 'landmark') ? $data->landmark : '0',
                    'locality' => property_exists($data, 'locality') ? $data->locality : '0'
                ];

                if (empty($custAddressId)) {
                    DB::table('gds_orders_addresses')->insert($custAddressArray);
                } else {
                    DB::table('gds_orders_addresses')
                            ->where(array('address_type' => $data->address_type,
                                'gds_order_id' => $otherInfo->gds_order_id))
                            ->update($custAddressArray);
                }
                $status = 1;
                $message = "Sucessfully";
            } else {
                $status = 0;
                $message = "No Channel Cust id.";
            }
            return json_encode(Array('Status' => $status, 'Message' => $message, 'channel_cust_id' => $channelCustId));
        } catch (ErrorException $ex) {
            return $ex->getMessage() . $ex->getTraceAsString();
        }
    }

    /**
     * [gdsOrderDetails retrives order details to use in the application]
     * @param  [json array] $orderdata [Contains order data from the commerce platform]
     * @return [array]            [order details]
     */
    public function gdsOrderDetails($orderdata) {

        try {
            $order_data = json_decode($orderdata['orderdata']);
            $custData = $order_data->customer_info;
            $channelId = $custData->channel_id;
            if (property_exists($order_data, 'address_info')) {
                $addressinfo = $order_data->address_info;
            } else {
                $status = 0;
                $message = 'No Address info.';
                return Response::json(array('Status' => $status, 'Message' => $message));
            }
            if (property_exists($order_data, 'order_info')) {
                $orderInfo = $order_data->order_info;
            }
            if (empty($orderInfo)) {
                $status = 0;
                $message = 'No Order info.';
                return Response::json(array('Status' => $status, 'Message' => $message));
            }
            $productDetails = array();
            if (property_exists($order_data, 'product_info')) {
                $productDetails = $order_data->product_info;
            }
            $legalEntityIds = array(); //For multiple items with different manufactures
            $ordersArray = array();

            if (!empty($productDetails)) {
                foreach ($productDetails as $product) {
                    $legal_entity_id = DB::table('products')->where('sku', $product->sku)->pluck('legal_entity_id')->all();

                    if (count($legal_entity_id) > 0) {
                        $legal_entity_id = $legal_entity_id[0];
                    } else
                        return $legal_entity_id = "";
                    $legalEntityIds[$legal_entity_id][] = $product;
                }
            }
            $orderIdsArr = array();
            foreach ($legalEntityIds as $manf => $productinfo) {

                $gds_orders = new GDSOrders;
                /**
                 * [$orderId getiing order id form gds_order and cheking/validating mp order id is exists 
                 *                                                                                 or not]
                 * @var [int]
                 */
                $orderId = $gds_orders->where(array('mp_order_id' => $orderInfo->channelorderid, 'mp_id' => $orderInfo->channelid, 'legal_entity_id' => $manf))
                        ->pluck('gds_order_id')->all();

                if (count($orderId) > 0)
                    $orderId = $orderId[0];
                else
                    $orderId = "";

                if (empty($orderId)) {
                    $sub_total = 0;
                    $tax = 0;
                    $total_amount = 0;
                    $shop_name = "";
                    foreach ($productinfo as $product) {
                        $sub_total+=$product->subtotal;
                        $tax+=$product->tax;
                        $total_amount+=$product->total;
                        if (isset($product->company)) {
                            $shop_name = $product->company;
                        } else {
                            $shop_name = "";
                        }
                    }
                    $order_status_id = DB::table('mp_status_mapping')
                            ->where('mp_status', $orderInfo->orderstatus)
                            ->pluck('ebutor_status_id')->all();

                    if (count($order_status_id) > 0)
                        $order_status_id = $order_status_id[0];
                    else
                        $order_status_id = 0;

                    if (!property_exists($custData, 'channel_user_id')) {
                        $userId = DB::table('gds_customer')
                                ->where(['email_address' => $custData->email_address, 'mp_id' => $custData->channel_id])
                                ->pluck('gds_cust_id')->all();
                        if (count($userId) > 0)
                            $gds_cust_id = $userId[0];
                        else
                            $gds_cust_id = 0;
                    }elseif (property_exists($custData, 'channel_user_id') && $custData->channel_user_id != '') {
                        $userId = DB::table('gds_customer')
                                ->where(['mp_user_id' => $custData->channel_user_id, 'mp_id' => $custData->channel_id])
                                ->pluck('gds_cust_id')->all();
                        if (count($userId) > 0)
                            $gds_cust_id = $userId[0];
                        else
                            $gds_cust_id = 0;
                    }elseif (property_exists($custData, 'channel_user_id') && $custData->channel_user_id == '') {
                        $userId = DB::table('gds_customer')
                                ->where(['email_address' => $custData->email_address, 'mp_id' => $custData->channel_id])
                                ->pluck('gds_cust_id')->all();
                        if (count($userId) > 0)
                            $gds_cust_id = $userId[0];
                        else
                            $gds_cust_id = 0;
                    }

                    /**
                     * if sales token is present the created by id will be the sales person
                     * order placing on behalf of the customer
                     */
                    $created_by = 0;
                    $platform_id = 0;
                    $cust_le_id = 0;
                    $dmapiOrders = new dmapiOrders();
                    
                    if (property_exists($custData , 'cust_le_id')) {
                        $cust_le_id = $custData->cust_le_id;
                    }else{
                        $cust_le_id = 0;
                    }
                    /**
                     * $customerLeId Gloable Variable To Use In Mongo
                     * @var int
                     */
                    $this->customerLeId =  $cust_le_id ;
                    if (property_exists($order_data, 'additional_info')) {

                        $additional_info = $order_data->additional_info;
                        $customer_token = 0;
                        $sales_token = 0;
                        /**
                         * Checking Property Exists Scheduled Delivery Date
                         */
                        if (property_exists($additional_info, 'scheduled_delivery_date')) {
                            $scheduled_delivery_date = date('Y-m-d',strtotime($additional_info->scheduled_delivery_date));
                        }else{
                            $scheduled_delivery_date = date( "Y-m-d", strtotime( date('Y-m-d')." +1 day" ) );
                        }

                        if (property_exists($additional_info, 'platform_id')) {

                            $platform_id = $additional_info->platform_id;
                        }

                        if (property_exists($additional_info, 'customer_token')) {

                            $customer_token = $additional_info->customer_token;
                        }

                        if (property_exists($additional_info, 'sales_token')) {

                            $sales_token = $additional_info->sales_token;
                        }

                        $actual_token = 0;
                        if ( $sales_token == "" ) {

                            $actual_token = $customer_token;
                            
                        } else {
                            
                            $actual_token = $sales_token;

                        }
                       
                        $created_by = $dmapiOrders->findUserIdByPassword($actual_token);
                        
                    }

                    $beat =  $dmapiOrders->getbeat($cust_le_id);
                    
                    date_default_timezone_set('Asia/Kolkata');
                    $gds_orders->mp_id = $orderInfo->channelid;
                    $gds_orders->mp_order_id = $orderInfo->channelorderid;
                    $gds_orders->legal_entity_id = $manf;
                    $gds_orders->order_status_id = 17016; //orderstatusid for INCORRECTORDER
                    $gds_orders->order_date = date('Y-m-d H:i:s');
                    $gds_orders->ship_total = $orderInfo->shippingcost;
                    $gds_orders->sub_total = $sub_total;
                    $gds_orders->tax_total = $tax;
                    $gds_orders->total = $total_amount;
                    $gds_orders->shop_name = $shop_name;
                    $gds_orders->gds_cust_id = $gds_cust_id;
                    $gds_orders->cust_le_id = $cust_le_id;
                    $gds_orders->order_expiry_date = date('Y-m-d H:i:s', strtotime($gds_orders->order_date . ' +30 day'));
                    $gds_orders->firstname = $order_data->customer_info->first_name;
                    $gds_orders->lastname = $order_data->customer_info->last_name;
                    $gds_orders->email = $order_data->customer_info->email_address;
                    $gds_orders->phone_no = $order_data->customer_info->mobile_no;
                    $gds_orders->created_by = $created_by;
                    $gds_orders->platform_id = $platform_id;
                    $gds_orders->scheduled_delivery_date = $scheduled_delivery_date;
                    $gds_orders->pref_slab1 = $order_data->additional_info->preferred_delivery_slot1;
                    $gds_orders->pref_slab2 = $order_data->additional_info->preferred_delivery_slot2;
                    $gds_orders->beat = $beat;
                    $gds_orders->mfc_id = $orderInfo->mfc_id;


                    $gds_orders->save();
                    $order_id = DB::getPdo()->lastInsertId();
                    $this->order_dump[$order_id]['order_total'] = $total_amount;
                    if (!empty($productinfo)) {
                        $gdsData = array();
                        foreach ($productinfo as $product) {
                            /*addressinfo is required to pick warehouseid*/
                            $status = $this->gdsOrderProducts($order_id, $product, $channelId, $order_status_id, $addressinfo);
                            if ($status == 2) {
                                $gdsData['api_key'] = $orderdata['api_key'];
                                $gdsData['secret_key'] = $orderdata['secret_key'];
                                $gdsData['channel_id'] = $orderInfo->channelid;
                                $gdsData['status'] = 0;
                                $gdsData['order_id'] = $order_id;
                                $this->GdsListingAndOrders($gdsData);
                            } else {
                                $status = 0;
                                return $status;
                            }
                        }
                    }

                    DB::table('gds_orders')
                            ->where('gds_order_id', $order_id)
                            ->update(['order_status_id' => $order_status_id,
                                'le_wh_id' => $this->le_warehouse_id]);
                } else {
                    $order_id = $orderId;
                }
                @$ebutorChannelId = Config('dmapi.channelid');

                /* do update orderstatus here */
                if (isset($ebutorChannelId) && $channelId == $ebutorChannelId) {

                    $this->updateChannelOrderIdFactail($order_id);
                }
                array_push($orderIdsArr, $order_id);
            }
            return $orderIdsArr;
        } catch (ErrorException $ex) {
            return 0;
        }
    }

    public function getTaxDetails($orderid) {

        try {
            $order = $this->_orderModel->getBillAndShipAddrFrmLE($orderid);

            if (isset($this->order_dump[$orderid]['state'])) {
                $state_id = $this->order_state[$orderid]['state'];
            } else {
                $address = $this->convertBillingAndShippingAddress($order);

                $address = (array) $address['shipping']; //
                $state_id = DB::table('zone')->where('name', $address['state_name'])
                        ->where('country_id', 99)
                        ->pluck('zone_id')->all();
                if (empty($state_id)) {
                    $state_id = 0;
                } else {
                    $state_id = $state_id[0];
                }
                $this->order_dump[$orderid]['state'] = $state_id;
            }

            if (isset($this->order_dump[$orderid]['taxType'])) {
                $taxtypes = $this->_order_dump[$orderid];
            } else {
                $taxtypes = $this->_orderModel->getTaxtypes();
                $this->order_dump[$orderid]['taxType'] = $taxtypes;
            }

            if (isset($this->order_dump[$orderid]['taxClasses'])) {
                
            } else {
                $taxClasses = $this->_orderModel->getTaxclasses();
                $this->order_dump[$orderid]['taxClasses'] = $taxClasses;
            }

            $total_tax = 0;
            if (isset($this->order_dump[$orderid]['products'])) {
                $products = $this->order_dump[$orderid]['products'];
                $product_warehousedump = $this->le_warehouses;
                $tax_product = array();
                $_warehouseModel = new warehouseModel();
                foreach ($products as $key => $product) {
                    $data['product_id'] = (int) $key;
                    $product_total = $product['total'];
                    $data['buyer_state_id'] = (int) $state_id;
                    $warehouseid = $product_warehousedump[$key];
                    $warehouse = $_warehouseModel->getwareHousedata($warehouseid);
                    if (count($warehouse) == 0) {
                        $warehouse_statecode = 4033;
                    } else {
                        $warehouse_statecode = $warehouse['state'];
                    }
                    $data['seller_state_id'] = (int) $warehouse_statecode;
                    $product_tax = 0;
                    $taxInfo = $this->getTaxInfo($data, $orderid);

                    $data = array();
                    if ($taxInfo != 'No data from API' && count($taxInfo) > 0) {

                        $data = array();
                        foreach ($taxInfo as $value) {

                            $taxClass_id = $value['Tax Class ID'];                       
                            $tax_temp = array('gds_order_prod_id' => $product['product_id'], 'tax_class' => $taxClass_id, 'tax' => $value['Tax Percentage']);
                            $product_tax += $value['Tax Percentage'];
                            $data[] = $tax_temp;
                        }

                        $actual_ammount = ($product_total * 100) / (100 + $product_tax);
                        $actual_ammount = round($actual_ammount, 5);
                        $product_tax_value = round(($product_total - $actual_ammount), 5);
                        $total_tax += $product_tax_value;
                        $gdsProducts = new gdsProducts();
                        /* Last Updated  29-07-2016 */
                        $gdsProducts->where('gds_order_prod_id', $product['product_id'])
                                ->update([
                                    'tax' => $product_tax_value,
                                    'price' => $actual_ammount
                        ]);
                        $tax_data = array();
                        foreach ($data as $value) {
                            $temp_tax_data = array();
                            $temp_tax_data = $value;
                            $percentage = $value['tax'];
                            if ($percentage == 0) {
                                $temp_tax_data['tax_value'] = 0;
                            }else{
                                $temp_tax_data['tax_value'] = $product_tax_value * ($percentage / $product_tax);
                            }                            
                            $tax_data[] = $temp_tax_data;
                        }
                    }

                    if (count($data) > 0) {
                        DB::table('gds_orders_tax')->insert($tax_data); /* Query Builder */
                    }
                }
            }

            return $total_tax;

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getTaxInfo($data, $orderid) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('APP_TAXAPI'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
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
        if ($err) {
            
        } else {

            $response = json_decode($response, true);
            if ($response) {
                if ($response['Status'] != 200) {
                    if (isset($response['ResponseBody'])) {
                        $args = array("ConsoleClass" => 'taxmail', 'arguments' => array('DmapiTaxTemplate', $orderid, json_encode($data), $response['ResponseBody']));
                    }else{
                        $args = array("ConsoleClass" => 'taxmail', 'arguments' => array('DmapiTaxTemplate', $orderid, json_encode($data), $response['Message']));  
                    }                    
                    $token = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
                    return 'No data from API';
                } else {

                    return $response['ResponseBody'];
                }
            } else {
                
            }
            return $response;
        }
    }

    public function GdsListingAndOrders($data) {
       // Log::info($data);
        try {
            $status = 0;
            $message = '';
            $channelid = $data['channel_id'];
            $charges_status = $data['status'];
            //\Log::info($data['status'] . '++++++++++++++++++++' . $data['channel_id']);
            if (isset($data['order_id'])) {
                $order_id = $data['order_id'];
            } else {
                $order_id = " ";
            }
            $channel_name = $this->custRepo->getChannelName($channelid);
            if (sizeof($channel_name) > 0) {
                $channel_name = $channel_name[0];
            }
           // Log::info($channel_name . '+++++++channel_name <- +++++++++++++++charges_status -> +++++' . $charges_status);
            $message = Parent::Charges($charges_status, $channel_name, $order_id);
            //Log::info($message . "+++++++++++++++++++++=message");
            $status = 1;
            if ($charges_status == 1)
                $message = "Successfully Listed the Product";
            else
                $message = "Successfully Updated Order Charges";
        } catch (ErrorException $ex) {
            $message = $ex->getMessage();
        }
        return json_encode(['Status' => $status, 'Message' => $message]);
    }

    public function gdschannelPayment($order_id, $order_data) {
        try {
            $payment_currency_id = DB::table('currency')->where('code', $order_data->paymentcurrency)->pluck('currency_id')->all();
            if (count($payment_currency_id) > 0)
                $payment_currency_id = $payment_currency_id[0];
            else
                $payment_currency_id = 0;

            $payment_method_id = DB::table('mp_status_mapping')->where('mp_status', $order_data->paymentmethod)->pluck('ebutor_status_id')->all();

            if (count($payment_method_id) > 0)
                $payment_method_id = $payment_method_id[0];
            else
                $payment_method_id = 0;

            $payment_status_id = DB::table('mp_status_mapping')->where('mp_status', $order_data->paymentstatus)->pluck('ebutor_status_id')->all();
            if (count($payment_status_id) > 0)
                $payment_status_id = $payment_status_id[0];
            else
                $payment_status_id = 0;

            $paymentId = DB::table('gds_orders_payment')
                    ->where(['transaction_id' => $order_data->transactionId,
                        'payment_method_id' => $payment_method_id,
                        'payment_status_id' => $payment_status_id,
                        'currency_id' => $payment_currency_id,
                        'amount' => $order_data->amount,
                        'gds_order_id' => $order_id])
                    ->pluck('orders_payment_id')->all();

            if (count($paymentId) > 0)
                $paymentId = $paymentId[0];
            else {

                date_default_timezone_set('Asia/Kolkata');
                DB::table('gds_orders_payment')->insert([
                    'gds_order_id' => $order_id,
                    'payment_method_id' => $payment_method_id,
                    'payment_status_id' => $payment_status_id,
                    'currency_id' => $payment_currency_id,
                    'amount' => $order_data->amount,
                    'transaction_id' => $order_data->transactionId,
                    'payment_date' => $order_data->paymentDate,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (ErrorException $ex) {
            return 0;
        }
    }

    public function gdsOrderProducts($order_id, $product, $channelId, $order_status_id, $addressinfo) {
        try {
            $productData = DB::table('products')
                    ->select(
                            'products.product_id', 'products.product_title as name', 'product_tot.product_name as productcontent_title', 'product_content.description', 'products.category_id', 'products.upc', 'products.sku', 'product_tot.rlp as b2b_unit_price', 'product_tot.cbp as b2c_unit_price', 'products.no_of_units', 'legal_entities.legal_entity_id as customer_id'
                    )
                    ->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'products.legal_entity_id')
                    ->leftJoin('product_content', 'products.product_id', '=', 'product_content.product_id')
                    ->leftJoin('product_tot', 'products.product_id', '=', 'product_tot.product_id')
                    ->where('products.sku', $product->sku)
                    ->first();
            $productId = DB::table('mp_product_add_update')->where([
                        'mp_id' => $channelId,
                        'product_id' => $productData->product_id
                    ])->pluck('product_id')->all();

            if (empty($productId)) {
                DB::table('mp_product_add_update')->insert([
                    'mp_id' => $channelId,
                    'product_id' => $productData->product_id
                ]);
            }

            $prd_id = DB::table('gds_order_products')->where([
                        'gds_order_id' => $order_id,
                        'product_id' => $product->scoitemid,
                        'qty' => $product->quantity])->pluck('product_id')->all();


            $channel_type = DB::table('mp')->where('mp_id', $channelId)->pluck('mp_type');

            if (count($channel_type) > 0)
                $channel_type = $channel_type[0];

            if ($channel_type == "B2B") {
                $productData->unit_price = $productData->b2b_unit_price;
            } elseif ($channel_type == "B2C") {
                $productData->unit_price = $productData->b2c_unit_price;
            }

            $status = 0;
            // var_dump($this->le_warehouse_id);
            // var_dump($this->le_warehouses);
            if (isset($this->le_warehouses[$product->scoitemid])) {
                // to keep the check for the flow of the data as per requirement
                $status = 2;
                $this->le_warehouse_id = $this->le_warehouses[$product->scoitemid];
            }

            if ($status == 2) {
                if (empty($prd_id)) {
                    try {

                        if ($productData->productcontent_title != "") {
                            /**
                             * Commented for taking the name from product table product_title(after freebee prduct name error)
                             * Last_updated: 17-10-2016 By: CS Tripathy 
                             */
                            //$product_name = $productData->productcontent_title;
                            $product_name = $productData->name;
                        } else {
                            $product_name = $productData->name;
                        }

                        /*                         * *Optional Esu quantity ***** */
                        if (isset($product->esu_quantity)) {
                            $esu_quantity = $product->esu_quantity;
                        } else {
                            $esu_quantity = NULL;
                        }

                        if (isset($product->parent_id)) {

                            if ($product->scoitemid == $product->parent_id) {
                                $parent_id = NULL;
                            } else {

                                $parent_id = $product->parent_id;
                            }
                        } else {
                            $parent_id = NULL;
                        }

                        $gds_product_id = DB::table('gds_order_products')->insertGetId([
                            'gds_order_id' => $order_id,
                            'product_id' => $product->scoitemid,
                            'mp_product_id' => $product->channelitemid,
                            'qty' => $product->quantity,
                            'mrp' => $product->price,
                            'price' => $product->subtotal,
                            'cost' => $product->subtotal,
                            'discount' => $product->discountprice,
                            'tax' => $product->tax,
                            'total' => $product->total, //$product->quantity * $product->price
                            'pname' => $product_name,
                            'upc' => $productData->upc,
                            'order_status' => $order_status_id,
                            'sku' => $productData->sku,
                            'unit_price' => $product->sellprice, //isset($productData->unit_price) ? $productData->unit_price : '',
                            'no_of_units' => $esu_quantity,
                            /* Parant ID */
                            'parent_id' => $parent_id
                        ]);


                        if ($gds_product_id) {

                            $this->order_dump[$order_id]['products'][$product->scoitemid]['product_id'] = $gds_product_id;
                            $this->order_dump[$order_id]['products'][$product->scoitemid]['total'] = $product->total;
                        }




                        //@$ebutorChannelId = Config('app.channelid');
                        return $status;
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }
                /* $channelitemid = DB::table('gds_order_shipping_details')->where([
                  'gds_order_id' => $order_id,
                  'mp_product_id' => $product->channelitemid])->pluck('channel_item_id');

                  if (empty($channelitemid))
                  {
                  DB::table('gds_order_shipping_details')->insert([
                  'gds_order_id' => $order_id,
                  'mp_product_id' => $product->channelitemid,
                  'service_cost' => $product->servicecost,
                  'dispatch_date' => $product->dispatchdate,
                  'min_time_to_dispatch' => $product->mintimetodispatch,
                  'max_time_to_dispatch' => $product->maxtimetodispatch,
                  'time_units' => $product->timeunits,
                  'created_at' => date('Y-m-d h:i:s')
                  ]);

                  } */
            } else {
                $status = 0;
                return $status;
            }
        } catch (ErrorException $ex) {
            return 0;
        }
    }

    public function getProductDynamicInfo($data) {
        try {
            $status = 0;
            $message = '';
            $product_id = isset($data['sku']) ? $data['sku'] : '';
            $productUpc = isset($data['upc']) ? $data['upc'] : '';
            if ($product_id == '' && $productUpc != '') {
                $product_id = DB::table('products')->where('upc', $productUpc)->pluck('sku')->all();
                if (count($product_id) > 0)
                    $product_id = $product_id[0];
                else
                    $product_id = "";
                if (empty($product_id)) {
                    return Response::json(Array('Status' => $status, 'Message' => 'No Data found', 'Data' => []));
                }
            }

            $manufacturerId = $this->apiAccess->getManufacturerId($data);
            $pincode = (isset($data['pincode']) && $data['pincode'] != '') ? $data['pincode'] : 0;
            $locationData = $this->getZonebyPincode($pincode, $manufacturerId);
            $locationDetails = json_decode($locationData);
            $locationId = $locationDetails->location_id;
            $ppid = $locationId;
            $finalarr = array();
            if (!empty($product_id)) {
                $products = DB::table('products')
                        ->select('products.*', 'categories.cat_name as cname', 'categories.category_id', 'brands.brand_name', 'brands.legal_entity_id')
                        ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
                        ->leftJoin('brands', 'brands.legal_entity_id', '=', 'products.legal_entity_id')
                        ->where(array('products.sku' => $product_id))
                        ->get()->all();
                if (!empty($products)) {
                    $childData = $this->getChildProductDynamicInfo($product_id);
                    $finalStaticsarr = array();
                    $finalDynamicarr = array();
                    $finalSlabarr = array();

                    $prodStaticattr = DB::Table('product_attributes')
                            ->select('product_attributes.value', 'attributes.attribute_code', 'attributes.name', 'attributes.attribute_id', 'attributes.attribute_group_id', 'attributes_groups.name as attribute_group_name')
                            ->leftJoin('attributes', 'attributes.attribute_id', '=', 'product_attributes.attribute_id')
                            ->leftJoin('attributes_groups', 'attributes_groups.attribute_group_id', '=', 'attributes.attribute_group_id')
                            ->where(array('product_attributes.product_id' => $products[0]->product_id))
                            ->get()->all();
                    $StaticAttributesarr = array();
                    $attributeGrouparr = array();
                    foreach ($prodStaticattr as $key1 => $value1) {
                        $StaticAttributesarr[$value1->name] = $value1->value;
                        if (in_array($value1->attribute_group_name, $attributeGrouparr)) {
                            array_push($attributeGrouparr[$value1->attribute_group_name], $StaticAttributesarr);
                        } else {
                            $attributeGrouparr[$value1->attribute_group_name] = $StaticAttributesarr;
                        }
                    }
                    $finalarr['product_name'] = $products[0]->name;
                    $finalarr['description'] = $products[0]->description;
                    //$finalarr['model_name'] = $products[0]->model_name;
                    $finalarr['upc'] = $products[0]->upc;
                    //$finalarr['ean'] = $products[0]->ean;
                    //$finalarr['jan'] = $products[0]->jan;
                    //$finalarr['isbn'] = $products[0]->isbn;
                    //$finalarr['mpn'] = $products[0]->mpn;
                    $finalarr['category_name'] = $products[0]->cname;
                    $finalarr['cost_price'] = $products[0]->cost_price;
                    $finalarr['min_order_quantity'] = $products[0]->min_order_quantity;
                    $finalarr['margin_price'] = $products[0]->b2b_margin_percentage;
                    $finalarr['mrp'] = $products[0]->mrp;
                    $finalarr['per_unit_price'] = $products[0]->b2b_unit_price;
                    $finalarr['dealer_unit_price'] = $products[0]->dealer_unit_price;
                    $finalarr['no_of_units'] = $products[0]->no_of_units;
                    $finalarr['upc'] = $products[0]->upc;
                    //$finalarr['dealer_price'] = $products[0]->mrp-(($products[0]->mrp/100)*$products[0]->margin_price);
                    $finalarr['dealer_price'] = round(($products[0]->mrp * 100) / (100 + $products[0]->b2b_margin_percentage), 2);
                    $finalarr['manufacturer_name'] = $products[0]->brand_name;
                    $finalarr['sku'] = $products[0]->sku;
                    $finalarr['attributes'] = $attributeGrouparr;

                    //Get Products Image Data              
                    $media = DB::table('product_media')
                            ->select('product_media.media_type', 'product_media.url')
                            ->leftJoin('products', 'products.product_id', '=', 'product_media.product_id')
                            ->where('product_media.product_id', $products[0]->product_id)
                            ->get()->all();
                    $doc_root = $_SERVER['SERVER_NAME'] . '/uploads/products/';
                    //echo '<pre/>';print_r($inTransitQty);exit; 
                    $mediaarr = array();
                    foreach ($media as $key3 => $value3) {
                        if (!empty($value3->media_type)) {
                            $imageUrl = $value3->url;
                            if (strpos($imageUrl, 'www') !== false || strpos($imageUrl, 'http') !== false) {
                                $mediaarr[$value3->media_type][] = $imageUrl;
                            } else {
                                $mediaarr[$value3->media_type][] = $doc_root . $imageUrl;
                            }
                        }
                    }
                    $finalarr['media'] = $mediaarr;

                    //Get Products slab rates              
                    $finalSlabarr = $this->getSlabRates($products[0]->product_id, $products[0]->mrp, $products[0]->b2b_margin_percentage, $products[0]->b2b_unit_price);
                    $finalarr['slab_rates'] = $finalSlabarr;

                    if ($ppid > 0) {
                        $inTransitQty = DB::table('product_inventory')
                                ->select('product_inventory.location_id', 'product_inventory.available_inventory')
                                ->leftJoin('products', 'products.product_id', '=', 'product_inventory.product_id');

                        $inTransitQty = $inTransitQty->where(array('product_inventory.product_id' => $products[0]->product_id, 'product_inventory.location_id' => $ppid));

                        /* $inTransitQty = DB::table('eseal_' . $products[0]->customer_id)
                          ->select('eseal_' . $products[0]->customer_id . '.primary_id')
                          ->leftJoin('track_history', 'track_history.track_id', '=', 'eseal_' . $products[0]->customer_id . '.track_id')
                          ->where(array('eseal_' . $products[0]->customer_id . '.level_id' => 0, 'eseal_' . $products[0]->customer_id . '.pid' => $products[0]->product_id, 'eseal_' . $products[0]->customer_id . '.gds_status' => 0, 'track_history.dest_loc_id' => $ppid))
                          ->groupBy('eseal_' . $products[0]->customer_id . '.pid')
                          ->count(); */
                    } else {
                        $inTransitQty = DB::table('product_inventory')
                                ->select('product_inventory.location_id', 'product_inventory.available_inventory')
                                ->leftJoin('products', 'products.product_id', '=', 'product_inventory.product_id')
                                ->where('product_inventory.product_id', $products[0]->product_id)
                                ->get()->all();
                    }
                    $finalarr['available_stock'] = $inTransitQty;
                    if (!empty($childData)) {
                        $finalarr['varients'] = $childData;
                    }
                    $status = 1;
                    $message = 'Data Successfully Retrieved.';
                } else {
                    $status = 0;
                    $message = 'No Data Retrieved.';
                }
            } else {
                return Response::json(['Status' => 0, 'Message' => 'Parameter Missing.']);
            }
        } catch (Exception $e) {
            $message = $e->getMessage() . ' - ' . $e->getTraceAsString();
        }
        return Response::json(Array('Status' => $status, 'Message' => $message, 'Data' => $finalarr));
    }

    public function getChildProductDynamicInfo($parentSku) {
        try {
            $status = 0;
            $message = '';
            $parentId = 0;
            if ($parentSku != '') {
                $parentId = DB::table('products')->where(array('sku' => $parentSku))->pluck('product_id')->all();
                if (count($parentId) > 0)
                    $parentId = $parentId[0];
            }else {
                return;
            }

            $product_id = $parentId;
            $ppid = 0;
            $finalarr = array();
            if ($product_id > 0) {
                $isParent = DB::table('products')->where(array('sku' => $parentSku))->pluck('is_parent')->all();
                if (count($isParent) > 0)
                    $isParent = $isParent[0];
                else
                    $isParent = "";
                if ($isParent == 0) {
                    
                }
                $childProducts = DB::table('products')
                        ->select('products.*', 'categories.cat_name as cname', 'categories.category_id', 'brands.brand_name', 'brands.legal_entity_id')
                        ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
                        ->leftJoin('brands', 'brands.legal_entity_id', '=', 'products.legal_entity_id')
                        ->leftJoin('product_relations', 'product_relations.product_id', '=', 'products.product_id')
                        ->where(array('product_relations.parent_id' => $product_id))
                        ->orWhere(array('products.product_id' => $product_id))
                        ->groupBy('products.product_id')
                        ->get()->all();
                if (!empty($childProducts)) {
                    $products = $childProducts;
                } else {
                    $products = DB::table('products')
                            ->select('products.*', 'categories.cat_name as cname', 'categories.category_id', 'brands.brand_name', 'brands.legal_entity_id')
                            ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
                            ->leftJoin('brands', 'brands.legal_entity_id', '=', 'products.legal_entity_id')
                            ->where(array('products.product_id' => $product_id))
                            ->get()->all();
                }
                foreach ($products as $product) {
                    $tempArray = array();
                    $finalStaticsarr = array();
                    $finalDynamicarr = array();
                    $finalSlabarr = array();

                    $tempArray['sku'] = $product->sku;
                    $tempArray['product_name'] = $product->title;
                    $tempArray['description'] = $product->description;
                    $tempArray['cost_price'] = $product->cost_price;
                    $tempArray['min_order_quantity'] = $product->min_order_quantity;
                    $tempArray['margin_price'] = $product->b2b_margin_percentage;
                    $tempArray['mrp'] = $product->mrp;
                    $tempArray['per_unit_price'] = $product->b2b_unit_price;
                    $tempArray['dealer_unit_price'] = $product->dealer_unit_price;
                    $tempArray['no_of_units'] = $product->no_of_units;
                    //$tempArray['model_name']      =   $product->model_name;
                    $tempArray['upc'] = $product->upc;
                    $tempArray['upc_type'] = $product->upc_type;
                    //$tempArray['ean']             =   $product->ean;
                    //$tempArray['isbn']            =   $product->isbn;
                    $brandDetails = $this->getBrandDetails($product->product_id);
                    $tempArray['brand_details'] = $brandDetails;
                    //$tempArray['dealer_price'] = $product->mrp-(($product->mrp/100)*$product->margin_price);
                    $tempArray['dealer_price'] = round(($product->mrp * 100) / (100 + $product->b2b_margin_percentage), 2);


                    //Products dynamic info
                    $prodStaticattr = DB::Table('product_attributes')
                            ->leftJoin('attributes', 'attributes.attribute_id', '=', 'product_attributes.attribute_id')
                            ->leftJoin('attribute_set_mapping', 'attribute_set_mapping.attribute_id', '=', 'product_attributes.attribute_id')
//                                ->leftJoin('attributes_groups', 'attributes_groups.attribute_group_id', '=', 'attributes.attribute_group_id')
//                                ->where(array('product_attributes.product_id' => $product->product_id, 'attributes.attribute_code' => 'quantity'))
                            ->where(array('product_attributes.product_id' => $product->product_id, 'attribute_set_mapping.is_varient' => 1, 'attribute_set_mapping.attribute_set_id' => $product->attribute_set_id))
                            ->select('attributes.attribute_code', 'product_attributes.value')
                            ->get()->all();
                    $varientAttributes = [];
                    if (!empty($prodStaticattr)) {
                        $remove = false;
                        foreach ($prodStaticattr as $productAttrData) {
                            if ($remove) {
                                if ($productAttrData->attribute_code != 'weight') {
                                    $varientAttributes[$productAttrData->attribute_code] = trim($productAttrData->value);
                                }
                            } else {
                                $varientAttributes[$productAttrData->attribute_code] = trim($productAttrData->value);
                            }
                        }
                    }
                    $prodStaticattr2 = DB::Table('product_attributes')
                            ->select('product_attributes.value', 'attributes.attribute_code', 'attributes.name', 'attributes.attribute_id', 'attributes.attribute_group_id', 'attributes_groups.name as attribute_group_name', 'attributes.is_varient')
                            ->leftJoin('attributes', 'attributes.attribute_id', '=', 'product_attributes.attribute_id')
                            ->leftJoin('attributes_groups', 'attributes_groups.attribute_group_id', '=', 'attributes.attribute_group_id')
                            ->where(array('product_attributes.product_id' => $product->product_id, 'attribute_type' => 1))
                            ->get()->all();

                    $attributeGrouparr = array();
                    foreach ($prodStaticattr2 as $key1 => $value1) {
                        //$StaticAttributesarr[$value1->name] = $value1->value;
                        $variableName = str_replace(' ', '_', $value1->name);
                        if (in_array($value1->attribute_group_name, $attributeGrouparr)) {
                            array_push($attributeGrouparr[$value1->attribute_group_name][$variableName], trim($value1->value));
                        } else {
                            $attributeGrouparr[$value1->attribute_group_name][$variableName] = trim($value1->value);
                        }
                    }
                    $tempArray['attributes'] = $attributeGrouparr;
                    $tempArray['varient_data'] = $varientAttributes;

                    //Get Products Image Data              
                    $media = DB::table('product_media')
                            ->select('product_media.media_type', 'product_media.url')
                            ->leftJoin('products', 'products.product_id', '=', 'product_media.product_id')
                            ->where('product_media.product_id', $product->product_id)
                            ->orderBy('product_media.product_id')
                            ->get()->all();
                    $doc_root = URL::to('/') . '/uploads/products/';
                    $mediaarr = array();
                    foreach ($media as $key3 => $value3) {
                        if (!empty($value3->media_type)) {
                            $imageUrl = $value3->url;
                            if (strpos($imageUrl, 'www') !== false || strpos($imageUrl, 'http') !== false) {
                                $mediaarr[$value3->media_type][] = $imageUrl;
                            } else {
                                $mediaarr[$value3->media_type][] = $doc_root . $imageUrl;
                            }
                        }
                    }
                    $tempArray['media'] = $mediaarr;

                    //Get Products slab rates              
                    $tempSlab = $this->getSlabRates($product->product_id, $product->mrp, $product->b2b_margin_percentage, $product->b2b_unit_price);
                    $finalSlabarr = $tempSlab;
                    $tempArray['slab_rates'] = $finalSlabarr;

                    if ($ppid > 0) {
                        $inTransitQty = DB::table('product_inventory')
                                ->select('product_inventory.location_id', 'product_inventory.available_inventory')
                                ->leftJoin('products', 'products.product_id', '=', 'product_inventory.product_id');
                        $inTransitQty = $inTransitQty->where(array('product_inventory.product_id' => $product->product_id, 'product_inventory.location_id' => $ppid));

                        /* $inTransitQty = DB::table('eseal_' . $product->customer_id)
                          ->select('eseal_' . $product->customer_id . '.primary_id')
                          ->leftJoin('track_history', 'track_history.track_id', '=', 'eseal_' . $product->customer_id . '.track_id')
                          ->where(array('eseal_' . $product->customer_id . '.level_id' => 0, 'eseal_' . $product->customer_id . '.pid' => $product->product_id, 'eseal_' . $product->customer_id . '.gds_status' => 0, 'track_history.dest_loc_id' => $ppid))
                          ->groupBy('eseal_' . $product->customer_id . '.pid')
                          ->count(); */
                    } else {
                        $inTransitQty = DB::table('product_inventory')
                                ->select('product_inventory.location_id', 'product_inventory.available_inventory')
                                ->leftJoin('products', 'products.product_id', '=', 'product_inventory.product_id')
                                ->where('product_inventory.product_id', $product->product_id)
                                ->get()->all();
                    }
                    $tempArray['available_stock'] = $inTransitQty;
                    $finalarr[] = $tempArray;
                }
                return $finalarr;

                $status = 1;
                $message = 'Data Successfully Retrieved.';
            } else {
                return Response::json(['Status' => 0, 'Message' => 'Parameter Missing.']);
            }
        } catch (Exception $e) {
            $message = $e->getMessage() . ' - ' . $e->getTraceAsString();
        }
        return Response::json(Array('Status' => $status, 'Message' => $message));
    }

    /**
     * [createShipment description]
     * @return [type] [description]
     */
    public function createShipment($orderdata) {

        $mandatoryFields = array('orderid',
            'channelorderid',
            'carrier',
            'trackingNumber',
        );
        $optionalFields = array('vehicle_number', 'rep_name');

        try {
           // Log::info($orderdata);
            $_orderModel = new dmapiOrders(); //new OrderModel();
            $_orderModuleModel = new OrderModel();
            $orderData = $orderdata['orderdata'];
            if (empty($orderData)) {
                $status = 400;
                $message = 'Request Format not correct.';
                return $this->returnJsonMessage($status, $message);
            }

            $order_data = json_decode($orderdata['orderdata']);
            $order_data_array = json_decode($orderdata['orderdata'], true);
            // if(isset($order_data_array['product_info'])){
            //     unset($order_data_array,)
            // }
            //check json to object conversion
            if (!$order_data || is_null($order_data)) {
                $status = 400;
                $message = "Request Json is not proper format please check";
                return $this->returnJsonMessage($status, $message);
            }

            if (!isset($order_data->orderid) && !isset($order_data->channelorderid)) {
                $status = 400;
                $message = "['mandatory field'] Both Orderid and ChannelOrderId is missing, Either one should be present";
                return $this->returnJsonMessage($status, $message);
            }

            if (!isset($order_data->orderitems)) {

                $status = 400;
                $message = "['mandatory field'] Product Info is missing..";
                return $this->returnJsonMessage($status, $message);
            }

            if (!isset($order_data->channelid)) {

                $status = 400;
                $message = "['mandatory field'] Channelid Info is missing..";
                return $this->returnJsonMessage($status, $message);
            } else {
                $channelId = $order_data->channelid;
            }

            if (isset($order_data->orderid) && property_exists($order_data, 'orderid') && isset($order_data->channelorderid) && property_exists($order_data, 'channelorderid')) {

                $orderId = $order_data->orderid;
                $orders = $_orderModuleModel->getOrderDetailById($orderId);

                if (isset($orders->mp_id)) {
                    if ($orders->mp_id != $order_data->channelid) {
                        $status = 400;
                        $message = " Channelid is invalid ...";
                        return $this->returnJsonMessage($status, $message);
                    }
                }

                if (isset($orders->mp_order_id)) {
                    if ($orders->mp_order_id != $order_data->channelorderid) {
                        $status = 400;
                        $message = " Channel Order id / Order id mismatch ...";
                        return $this->returnJsonMessage($status, $message);
                    }
                }
            } else if (isset($order_data->orderid) && property_exists($order_data, 'orderid')) {

                $orderId = $order_data->orderid;
                $orders = $_orderModuleModel->getOrderDetailById($orderId);
            } else if (isset($order_data->channelorderid) && property_exists($order_data, 'channelorderid')) {
                $channelOrderId = $order_data->channelOrderId;
                $mp_id = $order_data->channelid;
                $orders = $_orderModuleModel->getOrderDetailByChannelOrderId($channelOrderId, $mp_id);
            } else {
                $status = 400;
                $message = "['mandatory field'] Both Orderid or ChannelOrderId is missing, Either one should be present";
                return $this->returnJsonMessage($status, $message);
            }

            if (!$orders) {

                $status = 404;
                $message = "OrderId / ChannelOrderId not found ..";
                return $this->returnJsonMessage($status, $message);
            }

            $ebutor_status_id = $this->checkmapstatus($order_data->shippment_status, $channelId);

            if (!$ebutor_status_id) {

                $status = 404;
                $message = "Shipping Status mapping not found ..";
                return $this->returnJsonMessage($status, $message);
            }

            $orderId = $orders->gds_order_id;

            $isShipment = false;
            $hasShipmentArr = array('17002', '17009', '17013', '17015');
            if (in_array($orders->order_status_id, $hasShipmentArr)) {
                $isShipment = true;
            }

            if (!$isShipment) {

                $status = 400;
                $message = "Shippement can be done only after confirm";
                return $this->returnJsonMessage($status, $message);
            }

            $products = $_orderModuleModel->getProductByOrderId($orderId);

            $products_list_key = array();
            $order_skus = array();
            foreach ($products as $product) {

                $products_list_key[$product->sku] = $product->product_id;
                $order_skus[] = $product->sku;
            }

            $couriers = (array) $_orderModuleModel->getCouriers();
            $courier_dump = array();
            $courier_id = 0;
            $servicename_db = '';
            if (count($couriers) > 0) {

                foreach ($couriers as $courier) {

                    $courier_dump[$courier->carrier] = $courier->carrier_id;
                }
            } else {

                $status = 500;
                $message = "None of the courier services found ..";
                return $this->returnJsonMessage($status, $message);
            }

            if (!isset($order_data->carrier) || $order_data->carrier == '') {
                $status = 500;
                $message = "Carrier name is mandatory";
                return $this->returnJsonMessage($status, $message);
            } else if (array_key_exists($order_data->carrier, $courier_dump)) {

                $courier_id = $courier_dump[$order_data->carrier];

                if (!isset($order_data->servicename) || $order_data->servicename == '') {

                    $status = 500;
                    $message = " Services name mandatory ..";
                    return $this->returnJsonMessage($status, $message);
                } else {

                    $service_name = $_orderModuleModel->getShippingServiceName($courier_id);

                    if (count($service_name) > 0) {

                        $error = true;
                        foreach ($service_name as $key => $value) {
                            if ($value->service_name == $order_data->servicename) {
                                $servicename_db = $value->service_name;
                                $error = false;
                                break;
                            }
                        }

                        if ($error) {
                            $status = 500;
                            $message = "Service not associated with courier..";
                            return $this->returnJsonMessage($status, $message);
                        }
                    } else {
                        $servicename_db = '';
                    }

                    //check for Vehical Number, Representative name, Contact Number

                    $vehicle_number = isset($order_data->vehicle_number) ? $order_data->vehicle_number : '';
                    $representative_name = isset($order_data->representive_name) ? $order_data->representive_name : '';
                    //$representative_contact = isset($order_data->representative_contact)?$order_data->representative_contact:'';

                    if ($courier_dump[$order_data->carrier] == 8) {

                        if ($vehicle_number == '' || $representative_name == '') {

                            $status = 400;
                            $message = "Vehicle Number / Rep Name / Rep Contact is mandatory for self shippment.";
                            return $this->returnJsonMessage($status, $message);
                        }
                    }
                }
            } else {

                $status = 500;
                $message = "Provided courier service not found ..";
                return $this->returnJsonMessage($status, $message);
            }


            $tracking_number = '';

            if (isset($order_data->tracking_number) && property_exists($order_data, 'tracking_number')) {
                $tracking_number = $order_data->tracking_number;
            } else {

                $status = 500;
                $message = "Tracking number not found";
                return $this->returnJsonMessage($status, $message);
            }

            $shippedProductQtyArr = $_orderModuleModel->getShipmentQtyByOrderId($orderId);
            $availShippedQtyArr = $_orderModuleModel->getAvailableShippedQty($products, $shippedProductQtyArr);

            if (count($order_skus) == 0 || count($products_list_key) == 0) {

                $status = 404;
                $message = "Order does not contain any skus";
                return $this->returnJsonMessage(500, $message);
            }

            $errorAvailQty = array();
            $errorMsg = array();
            if (!$orderId) {
                $errorMsg[] = 'Invalid order id';
            }

            foreach ($order_data->orderitems as $shipmentProducts) {


                if (!isset($shipmentProducts->sku)) {
                    $errorMsg[] = "sku field missing";
                } else {
                    $sku = $shipmentProducts->sku;
                }

                if (!array_key_exists($sku, $products_list_key)) {
                    $errorMsg[] = "sku $sku does not belong to the order";
                } else {

                    if ($availShippedQtyArr[$products_list_key[$sku]] > 0 && ($availShippedQtyArr[$products_list_key[$sku]] >= $shipmentProducts->quantity)) {
                        $finalShippedItemArr[$products_list_key[$sku]] = $shipmentProducts->quantity;
                    } elseif ($availShippedQtyArr[$products_list_key[$sku]] == 0) {
                        $errorAvailQty[] = "$sku does not any left for shipping";
                    } elseif ($availShippedQtyArr[$products_list_key[$sku]] < $shipmentProducts->quantity) {
                        $errorAvailQty[] = "$sku has only " . $availShippedQtyArr[$products_list_key[$sku]] . "requested amount " . $shipmentProducts->quantity . "for shipping is more than the available shipping inventory";
                    }

                    if (!isset($shipmentProducts->quantity) || gettype($shipmentProducts->quantity) != 'integer') {
                        //echo "bad choice";
                        $errorMsg[] = "quantity given for $sku is not integer";
                    } else if ($shipmentProducts->quantity <= 0) {
                        $errorMsg[] = "quantity given for $sku is 0 or negative , has to be more than 0";
                    }
                }

                if (count($errorAvailQty)) {
                    $errorMsg[] = $errorAvailQty;
                    $errorMsg[] = 'You can not create shipment for more than available quantity.';
                }
                /*
                 * Save shipment grid and item
                 */
                if (count($errorMsg) > 0) {
                    return $this->returnJsonMessage(500, $errorMsg);
                } else if (is_array($finalShippedItemArr) && count($finalShippedItemArr) > 0 && count($finalShippedItemArr) == count($order_data->orderitems)) {

                    $shipGridId = $_orderModuleModel->saveShipmentGrid(array('gds_order_id' => $orderId, 'status_id' => $ebutor_status_id));
                    if ($shipGridId) {

                        $comment = isset($order_data->comment) ? $order_data->comment : '';

                        foreach ($finalShippedItemArr as $key => $shippedArray) {

                            $productId = $key; //$products_list_key[$sku];
                            $data = array('gds_ship_grid_id' => $shipGridId, 'product_id' => $productId, 'qty' => $shippedArray, 'status_id' => $ebutor_status_id, 'comment' => $comment);
                            $_orderModuleModel->saveShipmentGridItem($data);

                            /*
                             * Save shipment for tracking
                             */
                            $shipmentData = array('gds_ship_grid_id' => $shipGridId, 'product_id' => $key, 'qty' => $shippedArray, 'description' => $comment);
                            $_orderModuleModel->saveShipmentTrack($shipmentData);
                        }

                        $billingAndShippingArr = $_orderModuleModel->getBillAndShipAddrFrmLE($orderId);
                        $shippingArr = $this->convertBillingAndShippingAddress($billingAndShippingArr);

                        $trackDetail = $_orderModuleModel->getTrackingDetailByShipmentId($shipGridId);
                        $shipTrackId = isset($trackDetail->gds_ship_id) ? $trackDetail->gds_ship_id : 0;

                        $shipTrackData = array(
                            'gds_ship_grid_id' => $shipGridId,
                            'gds_order_id' => $orderId,
                            'ship_service_id' => $courier_id,
                            'ship_method' => $servicename_db,
                            'tracking_id' => $tracking_number,
                            'vehicle_number' => $vehicle_number,
                            'rep_name' => $representative_name,
                            'created_by' => $this->mfgId, //put api user id 
                            'ship_fname' => (isset($shippingArr['shipping']->fname) ? $shippingArr['shipping']->fname : ''),
                            'ship_lname' => (isset($shippingArr['shipping']->lname) ? $shippingArr['shipping']->lname : ''),
                            'ship_company' => (isset($shippingArr['shipping']->company) ? $shippingArr['shipping']->company : ''),
                            'ship_addr1' => (isset($shippingArr['shipping']->addr1) ? $shippingArr['shipping']->addr1 : ''),
                            'ship_addr2' => (isset($shippingArr['shipping']->addr2) ? $shippingArr['shipping']->addr2 : ''),
                            'ship_city' => (isset($shippingArr['shipping']->city) ? $shippingArr['shipping']->city : ''),
                            'ship_postcode' => (isset($shippingArr['shipping']->postcode) ? $shippingArr['shipping']->postcode : ''),
                            'ship_country_id' => (isset($shippingArr['shipping']->country_id) ? $shippingArr['shipping']->country_id : ''),
                            'ship_state_id' => (isset($shippingArr['shipping']->state_id) ? $shippingArr['shipping']->state_id : '')
                        );

                        $_orderModuleModel->saveTrackingDetail($shipTrackData, $shipTrackId);

                        /*
                         * Save comment
                         */

                        if (isset($order_data->comment)) {

                            $commentTypeId = $_orderModuleModel->getCommentTypeByName('SHIPMENT_STATUS');
                            $dataArr = array('entity_id' => $orderId, 'comment_type' => $commentTypeId,
                                'comment' => $order_data->comment, 'commentby' => $this->mfgId, //api user
                                'order_status_id' => $ebutor_status_id,
                                'created_at' => date('Y-m-d H:i:s'), 'comment_date' => date('Y-m-d H:i:s'));

                            $_orderModuleModel->saveComment($dataArr);
                        }
                        return Response::json(array('status' => 200, 'message' => 'Shipment created successfully.'));
                    } else {

                        $message = "Cant save the shipment data";
                        return $this->returnJsonMessage(500, $message);
                    }
                }
            }
        } catch (Exception $e) {
            $message = $e->getMessage() . ' - ' . $e->getTraceAsString();
            return $this->returnJsonMessage(500, $message);
        }
    }

    /**
     * [getOrderDetails description]
     * @return [json] [return the data of a particular order]
     */
    public function getOrderDetails($data) {

        try {
            $_orderModel = new dmapiOrders(); //new OrderModel();
            $_orderModuleModel = new OrderModel();
            $error = array();
            $orderId = 0;
            $channelId = 0;
            if (empty($data)) {
                $status = 400;
                $error[] = 'Request Format not correct.';
                return $this->returnJsonMessage($status, $error);
            }
            $order_data = json_decode($data['orderdata'], true);
            if (!$order_data || is_null($order_data)) {
                $status = 400;
                $error[] = "Request Json is not proper format please check";
                return $this->returnJsonMessage($status, $error);
            }

            if (!isset($order_data['orderid']) && !isset($order_data['channelorderid'])) {
                $status = 400;
                $error[] = " Both Orderid and ChannelOrderId is missing, Either one should be present";
                return $this->returnJsonMessage($status, $error);
            } else {

                if (isset($order_data['orderid'])) {

                    $orderId = $order_data['orderid'];
                }

                if (isset($order_data['channelorderid'])) {
                    $channelorderId = $order_data['channelorderid'];
                }
            }
            if (!isset($order_data['channelid'])) {

                $status = 400;
                $message = " Channelid is missing..";
                return $this->returnJsonMessage($status, $message);
            } else {
                $channelId = $order_data['channelid'];
            }

            $orders = NULL;
            if (isset($order_data['orderid']) && isset($order_data['channelorderid'])) {

                if (!empty($order_data['orderid']) && !empty($order_data['channelorderid'])) {
                    $orders = $_orderModuleModel->getOrderDetailById($orderId);

                    if (isset($orders->mp_id)) {
                        if ($orders->mp_id != $order_data['channelid']) {
                            $status = 400;
                            $message = " Channelid is invalid ...";
                            return $this->returnJsonMessage($status, $message);
                        }
                    }
                    if (isset($orders->mp_order_id)) {
                        if ($orders->mp_order_id != $order_data['channelorderid']) {
                            $status = 400;
                            $message = " Channel Order id / Order id mismatch ...";
                            return $this->returnJsonMessage($status, $message);
                        }
                    }
                } else {
                    $status = 400;
                    $message = " Channel Order id / Order id given as empty ...";
                    return $this->returnJsonMessage($status, $message);
                }
            } else {

                if (isset($order_data['orderid'])) {
                    if (!empty($order_data['orderid'])) {
                        $orders = $_orderModuleModel->getOrderDetailById($orderId);
                    } else {
                        $status = 400;
                        $message = "Order id given as empty ...";
                        return $this->returnJsonMessage($status, $message);
                    }
                    if (count($orders) == 0) {
                        $status = 0;
                        $inputdata = json_encode($data);
                        $message = "No orders exist with the given orderid ".$orderId.' input data '.$inputdata;
                        return $this->returnJsonMessage($status, $message);
                    }
                } else if (!isset($order_data['orderid']) && isset($order_data['channelorderid'])) {
                    if (!isset($order_data['channelid'])) {
                        $status = 0;
                        $message = "Channelid is mandatory ...";
                        return $this->returnJsonMessage($status, $message);
                    }
                    if (!empty($order_data['channelorderid']) && !empty($order_data['channelid'])) {
                        $orders = $_orderModuleModel->getOrderDetailByChannelOrderId($channelorderId, $channelId);
                    } else {
                        $status = 400;
                        $message = "Channel Order id/Channelid given as empty ...";
                        return $this->returnJsonMessage($status, $message);
                    }
                    if (count($orders) == 0) {
                        $status = 0;
                        $message = "No orders exist with the given Channel Orderid";
                        return $this->returnJsonMessage($status, $message);
                    } else {
                        $orderId = $orders->gds_order_id;
                    }
                }
            }

            $billingAndShippingArr = $_orderModuleModel->getBillAndShipAddrFrmLE($orderId);
            $address = $this->convertBillingAndShippingAddress($billingAndShippingArr);
            $products = $_orderModuleModel->getCompleteProductByOrderId($orderId);
            $products = json_decode(json_encode($products), true);

            $return_val = array();

            $orders = json_decode(json_encode($orders), true);

            if ((int) $orders['tax_total'] == 0) {

                if (!isset($this->order_dump[$orderId]['products'])) {

                    $_orderModuleModel = new OrderModel();
                    $products = $_orderModuleModel->getCompleteProductByOrderId($orderId);
                    $products = json_decode(json_encode($products), true);
                    foreach ($products as $product) {

                        $gds_order_prod_id = $product['gds_order_prod_id'];
                        $product_id = $product['product_id'];
                        $product_total = $product['total'];
                        $this->order_dump[$orderId]['products'][$product_id]['product_id'] = $gds_order_prod_id;
                        $this->order_dump[$orderId]['products'][$product_id]['total'] = $product_total;
                        $le_wh_id = $_orderModuleModel->getWarehouseId($product_id, $address['shipping']->postcode);
                        $this->le_warehouses[$product_id] = $le_wh_id;
                    }
                }

                $tax = $this->getTaxDetails($orderId);
                $taxupdate = DB::table('gds_orders')->where('gds_order_id', $orderId)
                        ->update(['tax_total' => $tax]);
                $orders = $_orderModuleModel->getOrderDetailById($orderId);
                $orders = json_decode(json_encode($orders), true);
            }

            if (isset($orders['order_status_id'])) {
                $order_stat = $_orderModel->checkOrderStatus($orders['order_status_id']);
            }
            if (!empty($orders)) {
                foreach ($orders as $key => $order) {
                    if ($key == 'order_status_id') {
                        $return_val['order_status'] = $order_stat[0];
                    } else {
                        $return_val[$key] = $order;
                    }
                }
                $slab1 = $return_val['pref_slab1'];
                $slab2 = $return_val['pref_slab2'];
                unset($return_val['pref_slab1']);
                unset($return_val['pref_slab2']);

                /**
                 * Discount details
                 */
                if ($orders['sub_total'] > $orders['grand_total']) {
                    $discount_amount = $orders['sub_total'] - $orders['grand_total'];
                    $return_val['discount_amount'] = $discount_amount;
                }else{
                    $return_val['discount_amount'] = 0;
                }
                
                $return_val['pref_slab1'] = is_null($slab1)?'':$_orderModel->checkOrderStatus($slab1);
                $return_val['pref_slab2'] = is_null($slab2)?'':$_orderModel->checkOrderStatus($slab2);
            } else {
                $status = 0;
                $message = 'No orders exist with the given OrderId '.$orderId;
                return $this->returnJsonMessage($status, $message);
            }
            if (isset($address['shipping'])) {
                $shipping_addr = json_decode(json_encode($address['shipping']), true);
            } else {
                $status = 0;
                $message = 'Order is IncorectOrder.No details Found';
                return $this->returnJsonMessage($status, $message);
            }
            foreach ($shipping_addr as $key => $add) {
                $return_val['shipping'][$key] = $add;
            }
            $shippinId = $_orderModel->getShipmentIdByOrderId($orderId);
            $track_details = array();
            foreach ($shippinId as $track) {
                $ship_grid_id = $track['gds_ship_grid_id'];
                $track_details[] = $_orderModel->getShipmentTrackDetails($ship_grid_id, $orderId);
            }
            foreach ($track_details as $key => $details) {

                $return_val['shipping_track_details'] = $details;
            }
            foreach ($products as $product) {
                $order_id = $product['gds_order_id'];
                $product_id = $product['product_id'];
                $prodorder_stat = $_orderModel->checkOrderStatus($product['order_status']);
                /**
                 * [$status getting order status by product from db view using dmapiOrders.php]
                 * @var [array]
                 */
                $status = $_orderModel->gdsOrderStatusTrack($product_id, $order_id);
                $statusArr = "";

                if ($status['shipments'] > 0) {
                    $statusArr['shipped'] = $status['shipments'];
                }
                if ($status['invoices'] > 0) {
                    $statusArr['invoiced'] = $status['invoices'];
                }
                if ($status['cancellations'] > 0) {
                    $statusArr['cancelled'] = $status['cancellations'];
                }
                if ($status['returns'] > 0) {
                    $statusArr['returned'] = $status['returns'];
                }
                if ($status['refunds'] > 0) {
                    $statusArr['refunded'] = $status['refunds'];
                }

                /**
                 * creating product array
                 */
                $return_val['products'][] = array(
                    'product_id' => isset($product['product_id']) ? $product['product_id'] : '',
                    'product_content_name' => isset($product['pname']) ? $product['pname'] : '',
                    'product_name' => isset($product['product_title']) ? $product['product_title'] : '',
                    'order_qty' => isset($product['qty']) ? (int) $product['qty'] : '',
                    'mrp' => isset($product['mrp']) ? $product['mrp'] : '',
                    'price' => isset($product['price']) ? $product['price'] : '',
                    'cost' => isset($product['cost']) ? $product['cost'] : '',
                    'tax' => isset($product['tax']) ? $product['tax'] : '',
                    'tax_class' => isset($product['tax_class']) ? $product['tax_class'] : '',
                    'upc' => isset($product['upc']) ? $product['upc'] : '',
                    'sku' => isset($product['sku']) ? $product['sku'] : '',
                    'seller_sku' => isset($product['seller_sku']) ? $product['seller_sku'] : '',
                    'unit_price' => isset($product['unit_price']) ? $product['unit_price'] : '',
                    'legal_entity_id' => isset($product['legal_entity_id']) ? $product['legal_entity_id'] : '',
                    'total' => isset($product['total']) ? $product['total'] : '',
                    'product_image' => isset($product['primary_image']) ? $product['primary_image'] : '',
                    'product_order_status' => isset($prodorder_stat[0]) ? $prodorder_stat[0] : '',
                    'status' => $statusArr
                );
            }
            $orderTrackArray[0] = array("status"=>"Placed","status_flag"=>1);
            $orderTrackArray[1] = array("status"=>"In Process","status_flag"=>-1);
            $orderTrackArray[2] = array("status"=>"Packed","status_flag"=>-1);
            $orderTrackArray[3] = array("status"=>"On The Way","status_flag"=>-1);
            $orderTrackArray[4] = array("status"=>"Delivered","status_flag"=>-1);
            $order_status_id = $orders['order_status_id'];
            if($order_status_id == 17001 || $order_status_id == 17002){
                $orderTrackArray[1]['status_flag'] = 0;
            }else if($order_status_id == 17005 || $order_status_id == 17020){
                $orderTrackArray[1]['status_flag'] = 1;
                $orderTrackArray[2]['status_flag'] = 0;
            }else if($order_status_id == 17021){
                $orderTrackArray[1]['status_flag'] = 1;
                $orderTrackArray[2]['status_flag'] = 1;
                $orderTrackArray[3]['status_flag'] = 0;
            }else if($order_status_id == 17014 || $order_status_id == 17026){
                $orderTrackArray[1]['status_flag'] = 1;
                $orderTrackArray[2]['status_flag'] = 1;
                $orderTrackArray[3]['status_flag'] = 1;
                $orderTrackArray[4]['status_flag'] = 0;
            }else if($order_status_id == 17023 || $order_status_id == 17007){
                $orderTrackArray[1]['status_flag'] = 1;
                $orderTrackArray[2]['status_flag'] = 1;
                $orderTrackArray[3]['status_flag'] = 1;
                $orderTrackArray[4]['status_flag'] = 1;
                $orderTrackArray[5] = array("status"=>"Completed","status_flag"=>0);
            }else if($order_status_id == 17008){
                $orderTrackArray[1]['status_flag'] = 1;
                $orderTrackArray[2]['status_flag'] = 1;
                $orderTrackArray[3]['status_flag'] = 1;
                $orderTrackArray[4]['status_flag'] = 1;
                $orderTrackArray[5] = array("status"=>"Completed","status_flag"=>1);
            }else if($order_status_id == 17022){
                $orderTrackArray[1]['status_flag'] = 1;
                $orderTrackArray[2]['status_flag'] = 1;
                $orderTrackArray[3]['status_flag'] = 1;
                $orderTrackArray[4] = array("status"=>"Returned","status_flag"=>1);
            }else if($order_status_id == 17015 || $order_status_id == 17009){
                $orderTrackArray[1]['status_flag'] = 1;
                $orderTrackArray[2] = array("status"=>"Cancelled","status_flag"=>1);
                unset($orderTrackArray[3]);
                unset($orderTrackArray[4]);
            }
            
            $return_val['delivery_slot'] = $orders['pdp'] . ',' . $orders['pdp_slot'];
            $return_val['order_track'] = $orderTrackArray;
            return $this->returnJsonMessage(1, $return_val);
        } catch (Exception $e) {
            $message = $e->getMessage() . ' - ' . $e->getTraceAsString();
            return $this->returnJsonMessage(500, $message);
        }
    }

    /**
     * convertBillingAndShippingAddress() method is used to convert billing and shipping
     * address in array format
     * @param $billingAndShippingArr Array
     * @return Array
     */
    private function convertBillingAndShippingAddress($billingAndShippingArr) {
        $billingAndShipping = array();
        foreach ($billingAndShippingArr as $billingAndShippingData) {
            if ($billingAndShippingData->address_type == 'shipping') {
                $billingAndShipping['shipping'] = $billingAndShippingData;
            }

            if ($billingAndShippingData->address_type == 'billing') {
                $billingAndShipping['billing'] = $billingAndShippingData;
            }
        }

        if(count($billingAndShipping)==1) {

                        $billingAndShipping['billing'] = $billingAndShipping['shipping'];                       

        }
        return $billingAndShipping;
    }

    /**
     * [checkmapstatus description]
     * @param  [type] $mp_status [description]
     * @param  [type] $channelId [description]
     * @return [type]            [description]
     */
    private function checkmapstatus($mp_status, $channelId) {
        // check the status in the mapping_status table
        $checkmapstatus = DB::table('mp_status_mapping as ms')
                ->select('*')
                ->where('mp_status', $mp_status)
                ->first();
        if (!empty($checkmapstatus->ebutor_status_id)) {
            // $checkStatus = DB::table('master_lookup as ml')
            //         ->select('*')
            //         ->join('master_lookup_categories as mlc', 'mlc.mas_cat_id', '=', 'ml.mas_cat_id')
            //         ->where('mlc.mas_cat_name', '=', 'Order Status')
            //         ->where('ml.value', '=', $checkmapstatus->ebutor_status_id)
            //         ->first();
            return $checkmapstatus->ebutor_status_id;
        } else {
            return false;
        }
    }

    /**
     * [createInvoice Creates Invoice against order with available quantity]
     * @param  [json array] $orderData [Holds order data in json format]
     * @return [json message]            [returns json with status and message]
     */
    public function createInvoice($orderData) {
        try {

            $_orderModel = new OrderModel();
            $orderData = json_decode($orderData['orderdata']);
            $orderData = json_decode(json_encode($orderData), True);
            if (!isset($orderData) || !isset($orderData['gds_order_id'])) {
                $response = array('status' => 400, 'message' => 'Invalid order data.');
                return Response::json($response);
            } else {
                $postData = $orderData;
            }

            if (!isset($orderData['invoice_item']) || $orderData['invoice_item'] == "") {
                $response = array('status' => 401, 'message' => 'Invalid invoice item.');
                return Response::json($response);
            }
            if (!isset($orderData['available_qty']) || $orderData['available_qty'] == "") {
                $response = array('status' => 402, 'message' => 'Invalid available quantity.');
                return Response::json($response);
            }
            if (!isset($orderData['invoice_status']) || $orderData['shipment_grid_id'] == "") {
                $response = array('status' => 403, 'message' => 'Invalid invoice status.');
                return Response::json($response);
            }
            if (!isset($orderData['shipment_grid_id']) || $orderData['shipment_grid_id'] == "") {
                $response = array('status' => 406, 'message' => 'Shipment id is missing.');
                return Response::json($response);
            }

            $total = 0;
            $tax = 0;
            $discount = 0;
            $qty = 0;
            $orderId = isset($postData['gds_order_id']) ? (int) $postData['gds_order_id'] : 0;
            $shipment_grid_id = isset($postData['shipment_grid_id']) ? (int) $postData['shipment_grid_id'] : 0;
            /**
             * [$totalShipments getting shippment quantity for validation]
             * @var [int]
             */
            $totalShipments = (int) $_orderModel->getShipmentCount($orderId);
            $invoicedQtyByitem = $_orderModel->getItemInvoicedQtyByOrderId($orderId);
            $shipmentQtyByitem = $_orderModel->getShipmentQtyByOrderId($orderId);
            $totalInvoiceQty = 0;
            foreach ($invoicedQtyByitem as $key => $inv) {
                $totalInvoiceQty = $totalInvoiceQty + $inv;
            }
            $totalShipmentQty = 0;
            foreach ($shipmentQtyByitem as $key => $shipment) {
                $totalShipmentQty = $totalShipmentQty + $shipment;
            }
            if ($totalInvoiceQty >= $totalShipmentQty) {
                $response = array('status' => 405, 'message' => 'No item to ship.');
                return Response::json($response);
            }
            $enbaleCreateInvoice = false;
            if ($totalShipments > 0) {
                $enbaleCreateInvoice = true;
            }
            if (!$enbaleCreateInvoice) {
                $response = array('status' => 404, 'message' => 'No shipment done yet.');
                return Response::json($response);
            }
            $orderComment = isset($postData['order_comment']) ? (string) $postData['order_comment'] : '';
            $commentDate = date('Y-m-d');
            if ($orderId > 0) {
                $order = $_orderModel->getOrderStatusById($orderId);
                if (!isset($order->gds_order_id)) {
                    $response = array('status' => 304, 'message' => 'Invalid order id');
                } else {

                    foreach ($postData['invoice_item'] as $key => $value) {
                        if ($postData['available_qty'][$value] != 0) {
                            $order_product = $_orderModel->getProductByOrderIdProductId($orderId, $value);
                            $shipmentId = dmapiOrders::getShipmentIdByOrderIdProductId($orderId, $value);
                            if ($shipmentId['gds_ship_grid_id'] != $shipment_grid_id) {
                                $response = array('status' => 407, 'message' => 'Shipping id grid miss matched.');
                                return Response::json($response);
                            }
                            if (in_array(null, $invoicedQtyByitem)) {
                                $invQty = $invoicedQtyByitem[$value];
                            }
                            $total = $total + $postData['available_qty'][$value] * $order_product->price;
                            $tax = $tax + $order_product->tax;
                            $discount = $discount + $order_product->discount;
                            $qty = $qty + $postData['available_qty'][$value];
                            $currncy_id = $order_product->currency_id;
                            $price = $order_product->price;
                            $cust_name = $order_product->firstname . ' ' . $order_product->lastname;
                            $gds_invoice_items[] = [
                                'gds_order_id' => $orderId,
                                'tax_amount' => $order_product->tax,
                                'discount_amount' => $order_product->discount,
                                'row_total' => $order_product->price + $order_product->tax + $order_product->discount,
                                'qty' => $postData['available_qty'][$value],
                                'price' => $order_product->price,
                                'product_id' => $value,
                                'invoice_status' => $postData['invoice_status'],
                            ];
                        }
                    }
                    if ($qty != 0) {
                        $currencycode = $_orderModel->getCurrencyCode($currncy_id);
                        $gds_invoice_grid = [
                            'grand_total' => $total,
                            'gds_order_id' => $orderId,
                            'currency_code' => $currencycode,
                            'gds_ship_grid_id' => $shipment_grid_id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'invoice_status' => $postData['invoice_status'],
                            'billing_name' => $cust_name
                        ];
                        $invoice_grid_id = $_orderModel->invoiceGrid($gds_invoice_grid);

                        $gds_order_invoice = [
                            'gds_invoice_grid_id' => $invoice_grid_id,
                            'tax_amount' => $tax,
                            'total_qty' => $qty,
                            'grand_total' => $total,
                            'subtotal' => $total,
                            'discount_amount' => $discount,
                            'billing_address_id' => '',
                            'currency_code' => $currencycode,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                        $order_invoice_id = $_orderModel->gdsOrderInvoice($gds_order_invoice);
                        foreach ($gds_invoice_items as $key => $value) {
                            $value['gds_order_invoice_id'] = $order_invoice_id;
                            if ($value['qty'] != 0) {
                                $invoice_item_id = $_orderModel->invoiceGridItems($value);
                            }
                        }
                        if ($invoice_grid_id) {
                            $commentTypeId = $_orderModel->getCommentTypeByName('INVOICE_STATUS');
                            $gds_orders_comments = [
                                'entity_id' => $orderId,
                                'comment' => $postData['order_comment'],
                                'order_status_id' => $postData['invoice_status'],
                                'comment_type' => $commentTypeId,
                                'commentby' => '1', //By '1' super admin for api purpose only
                                'comment_date' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s')
                            ];
                            $_orderModel->saveComment($gds_orders_comments);
                            return Response::json(array(
                                        'status' => 200,
                                        'invoice_id' => $invoice_grid_id,
                                        'message' => 'Successfully Invoice Generated.'
                                            )
                            );
                        }
                    }
                }
            } else {
                $response = array('status' => 400, 'message' => 'Invalid order input data.');
            }
            return Response::json($response);
        } catch (ErrorException $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => 'Invalid request API'));
        }
    }

    private function returnJsonMessage($status, $message) {
        if ($status == 0) {
            $this->orderFailureMail($message);
        }
        Log::info(json_encode(array('Status' => $status, 'Message' => $message)));
        return Response::json(array('Status' => $status, 'Message' => $message));
    }

    public function orderFailureMail($msg) {

        $data = $msg ;
        $mail = \Mail::raw($data, function ($message) {
            $message->from('tracker@ebutor.com', 'Ebutor');
            $message->subject('Order failed ');
            $message->to('tech@ebutor.com');
        });
        if ($mail) {
            return 'mail sent successfully';
        } else {
            return 'not sent';
        }
    }

    public function createshipmentbypicklist($data){
        Session::put('userId', 'developer');
        $_orderController = new OrdersController();
        $shipData = $_orderController->createShipmentByPicklistApiAction($data);
        return $shipData;
    }

    /**
     * [rectifyTaxOnOrderId description]
     * @param  [type] $data = $data = array(
        'api_key' => 'cp_prod',
        'secret_key' => '1TqUmN38d6fak6AZ',
        'orderId' => '{"orderid":"'.$orderID.'","channelid":"1"}'
        )
     * @return [type]          [description]
     */
    public function rectifyTaxOnOrderId($data){

        $data = json_decode($data['order_data'],true);
        $orderId = $data['orderId'];
        /**
         * [$dmapiOrders update tax in gds_order to zero]
         * @var dmapiOrders
         */
        $dmapiOrders = new dmapiOrders();
        $updateTax = $dmapiOrders->updateTaxToZero($orderId);

        if($updateTax){
            /*
                delete all gds_tax entry using the gds_order_product from gds_order_products
             */
            $dmapiOrders->deleteGdsTaxFeilds($orderId);
            $data = '{"orderid":"'.$orderId.'","channelid":"1"}';
            $this->getOrderDetails($data);
            return true;
        }else{

            return false;
        }
    }

    /**
     * [saveComment description]
     * @param  [type] $orderId     [description]
     * @param  [type] $commentType [description]
     * @param  [type] $dataArr     [description]
     * @return [type]              [description]
     */
    private function saveComment($orderId, $commentType, $dataArr) {
        $typeId = $this->_orderModel->getCommentTypeByName($commentType);
        $date = date('Y-m-d H:i:s');
        $commentArr = array('entity_id'=>$orderId, 'comment_type'=>$typeId,
                        'comment'=>(string)$dataArr['comment'],
                        'commentby'=>Session('userId'),
                        'order_status_id'=>$dataArr['order_status_id'],
                        'created_at'=>(string)$date,
                        'comment_date'=>(string)$date
                        );

        $this->_orderModel->saveComment($commentArr);
    }

    public function convertBillAndShipAddrLE($billingAndShippingArr) {
        try{
                $billingAndShipping = array();
                if(isset($billingAndShippingArr) && is_array($billingAndShippingArr) && count($billingAndShippingArr) > 0) {
                    foreach($billingAndShippingArr as $billingAndShippingData) {
                            $billingAndShipping['shipping'] = $billingAndShippingData;
                            $billingAndShipping['billing'] = $billingAndShippingData;
                    }
                }
                return $billingAndShipping;
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    /**
     * slackMsg for sending message to slack
     * @param  string $msg [text to send]
     * @param  string $channel [name of the slack group]
     * @return void      [description]
     */
    public function slackMsg($msg, $channel, $order_id = '')
    {
        $url = 'https://hooks.slack.com/services/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';
        $data['channel'] = $channel;
        $data['username'] = 'DmApiV2';
        if (!is_null($order_id)) {
            $data['text'] = '#NEW ORDER:'.$order_id.'\n'.$msg;
        }else{
            $data['text'] = $msg; 
        }
        $data['icon_url'] = 'http://portal1.ebutor.com/assets/admin/layout/img/small-logo.png';

        $request['payload'] = json_encode($data, true);
        $ch = curl_init();        
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$request);
        $buffer = curl_exec($ch);
        if(empty ($buffer)){ 
            return false;
        }else{ 
            return true;
        } 
    }
}
