<?php

namespace App\Modules\Cpmanager\Controllers;

use Illuminate\Support\Facades\Input;
use Session;
use Response;
use Log;
use URL;
use DB;
use Illuminate\Http\Request;
use App\Modules\Cpmanager\Models\CartModel;
use App\Modules\Cpmanager\Models\catalog;
use App\Modules\Cpmanager\Models\Review;
use App\Modules\Cpmanager\Models\HomeModel;
use App\Http\Controllers\BaseController;
use App\Modules\Cpmanager\Models\ContainerapiModel;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Orders\Models\AutoAssignPickerCheckerModel;
use Cache;

class CartController extends BaseController {

    public function __construct() {

        $this->cart = new CartModel();
        $this->Review = new Review();
        $this->catalog = new catalog();
        $this->homepage = new HomeModel();
    }

    /*
     * Class Name: addcart
     * Description: adding & updating products to cart 
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 8th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function addcart() {

        try {

            if ($_POST['data'] != '') {
                $array = json_decode($_POST['data'], true);
                if (isset($array['customer_token']) && $array['customer_token'] != '') {
                    $val = $this->cart->valToken($array['customer_token']);
                    if ($val['token_status'] == 1) {
                        $products_array_size = sizeof($array['products']);
                        for ($i = 0; $i < $products_array_size; $i++) {
                            $products_main_array = $array['products'][$i];
                            $product_id = $products_main_array['product_id'];
                            $varientsarray = $products_main_array['variants'];

                            if ($product_id == "") {
                                return json_encode(array('status' => "failed", 'message' => 'ProductId not sent', 'data' => ""));
                            }
                            if (empty($varientsarray)) {
                                return json_encode(array('status' => "failed", 'message' => 'variants not sent', 'data' => ""));
                            }
                            $varient_array_size = sizeof($varientsarray);

                            for ($j = 0; $j < $varient_array_size; $j++) {
                                $varient_id = $varientsarray[$j]['variant_id'];
                                $unit_price = $varientsarray[$j]['unit_price'];
                                $applied_margin = $varientsarray[$j]['applied_margin'];
                                $total_qty = $varientsarray[$j]['total_qty'];
                                $total_price = $varientsarray[$j]['total_price'];
                                $size_of_packs = sizeof($varientsarray[$j]['packs']);

                                if ($varient_id == "") {
                                    return json_encode(array('status' => "failed", 'message' => 'variantId is not set', 'data' => ""));
                                } else if ($unit_price == "") {
                                    return json_encode(array('status' => "failed", 'message' => 'unit price is not set', 'data' => ""));
                                } else if ($applied_margin == "") {
                                    return json_encode(array('status' => "failed", 'message' => 'margin is not set', 'data' => ""));
                                } else if ($total_qty == "") {
                                    return json_encode(array('status' => "failed", 'message' => 'quantity is not set', 'data' => ""));
                                } else if ($total_price == "") {
                                    return json_encode(array('status' => "failed", 'message' => 'total price is not set', 'data' => ""));
                                }
                                $segmentId = (isset($array['segment_id']) && $array['segment_id'] != '') ? $array['segment_id'] : 0;
                                $this->cart->checkInventory($varient_id, $array['le_wh_id'], $segmentId, $total_qty);
                                if (empty($size_of_packs)) {
                                    return json_encode(array('status' => "failed", 'message' => 'Packs not sent', 'data' => ""));
                                }
                                for ($k = 0; $k < $size_of_packs; $k++) {
                                    $packsArray = $varientsarray[$j]['packs'];
                                    $pack_qty = $packsArray[$k]['pack_qty'];
                                }
                            }
                        }
                        if ($val['token_status'] == 1) {
                            $customerId = $this->cart->getcustomerId($array['customer_token']);
                            $addtocart = $this->cart->addtocart($varientsarray, $array['customer_token'], $customerId);
                            return json_encode(array('status' => "success", 'message' => 'savecart', 'data' => $addtocart));
                        }
                    } else {
                        return json_encode(array('status' => "session", 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
                    }
                } else {
                    return json_encode(array('status' => "failed", 'message' => 'customer token is not sent', 'data' => ""));
                }
            } else {
                $error = "Please pass required parameters";
                return json_encode(array('status' => "failed", 'message' => $error, 'data' => []));
            }
        } catch (Exception $e) {
            $message = "Internal server error";
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Array('status' => "failed", 'message' => $message, 'data' => []);
        }
    }

    /*
     * Class Name: deletecart
     * Description: delete product in cart 
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 8th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function deletecart() {

        try {
            if ($_POST['data'] != '') {
                $array = json_decode($_POST['data'], true);
                if (isset($array['customer_token']) && $array['customer_token'] == '') {
                    return json_encode(array('status' => "failed", 'message' => 'Token not sent', 'data' => []));
                }
                if (isset($array['isClearCart']) && $array['isClearCart'] == '') {
                    return json_encode(array('status' => "failed", 'message' => 'isClearCart flag not sent', 'data' => []));
                }
            } else {
                $error = "Please pass required parameters";
                return json_encode(array('status' => "failed", 'message' => $error, 'data' => []));
            }
            $val = $this->cart->valToken($array['customer_token']);
            if ($val['token_status'] == 0) {
                return json_encode(array('status' => "session", 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => ""));
            }
            if ($val['token_status'] == 1) {
                $deletecart = $this->cart->deletecart($array);
                return json_encode(array('status' => "success", 'message' => 'Delete cart', 'data' => "Your product was successfully deleted"));
            }
        } catch (Exception $e) {
            $status = "failed";
            $message = "Internal server error";
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            $data = [];
            return Array('status' => $status, 'message' => $message, 'data' => $data);
        }
    }

    /*
     * Class Name: Viewcart
     * Description: We can show products in the cart
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 11th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function Viewcart() {

        try {
            if ($_POST['data'] != '') {
                $array = json_decode($_POST['data'], true);
                if (isset($array['customer_token']) && $array['customer_token'] == '') {
                    return json_encode(array('status' => "failed", 'message' => 'Token not sent', 'data' => ""));
                }
                $val = $this->cart->valToken($array['customer_token']);

                if ($val['token_status'] == 0) {
                    return json_encode(array('status' => "session", 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => ""));
                }
                if ($val['token_status'] == 1) {
                    $customerId = $this->cart->getcustomerId($array['customer_token']);
                    $getResults = $this->cart->getViewcartData($customerId);

                    $size_product_Array = sizeof($getResults);
                    $data = array();

                    $k = 0;
                    for ($i = 0; $i < $size_product_Array; $i++) {
                        $data[$k] = array(
                            'cartId' => $getResults[$i]['cartId'],
                            'product_id' => $getResults[$i]['product_id'],
                            'name' => $getResults[$i]['Name'],
                            //'rating' => $rating,
                            'total_price' => $getResults[$i]['total_price'],
                            'date_added' => $getResults[$i]['created_at']
                        );

                        $variants = $this->cart->variant($getResults[$i]['product_id'], $customerId);
                        $l = 0;
                        for ($j = 0; $j < count($variants); $j++) {
                            $data[$k]['variants'][$l] = $variants[$j];
                            $packs = $this->cart->getPackdata($getResults[$i]['product_id'], $array['le_wh_id'], $customerId);

                            for ($c = 0; $c < count($packs); $c++) {
                                $data[$k]['variants'][$l]['pack'][$c] = array(
                                    'pack_size' => $packs[$c]['pack_size'],
                                    'margin' => $packs[$c]['margin'],
                                    'unit_price' => $packs[$c]['unit_price'],
                                    'dealer_price' => $packs[$c]['dealer_price'],
                                    'qty' => '');
                            }
                            $l++;
                        }$k++;
                    }

                    if (!empty($data)) {
                        return json_encode(array('status' => "success", 'message' => 'ViewCart', 'data' => $data));
                    } else {
                        return json_encode(array('status' => "success", 'message' => 'ViewCart', 'data' => []));
                    }
                }
            } else {
                $error = "Please pass required parameters";
                return json_encode(array('status' => "failed", 'message' => $error, 'data' => []));
            }
        } catch (Exception $e) {
            $status = "failed";
            $message = "Internal server error";
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            $data = [];
            return Array('status' => $status, 'message' => $message, 'data' => $data);
        }
    }

    /*
     * Class Name: cartcount
     * Description: Count of products in cart
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 13th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function cartcount() {

        try {

            if ($_POST['data'] != '') {
                $array = json_decode($_POST['data'], true);
                if (isset($array['customer_token']) && $array['customer_token'] == '') {
                    return json_encode(array('status' => "failed", 'message' => 'Token not sent', 'data' => ""));
                }
                $val = $this->cart->valToken($array['customer_token']);

                if ($val['token_status'] == 0) {
                    return json_encode(array('status' => "session", 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => ""));
                }
                if ($val['token_status'] == 1) {

                    $customerId = $this->cart->getcustomerId($array['customer_token']);
                    $cart_count = $this->cart->cartcount($customerId);
                    return json_encode(array('status' => "success", 'message' => 'cartcount', 'data' => $cart_count));
                }
            } else {
                $error = "Please pass required parameters";
                return json_encode(array('status' => "failed", 'message' => $error, 'data' => []));
            }
        } catch (Exception $e) {
            $message = "Internal server error";
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Array('status' => "failed", 'message' => $message, 'data' => []);
        }
    }

    /*
     * Class Name: Editcart
     * Description: The function is used to edit cart details
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 9th Aug 2016
     * Modified Date & Reason: 
      added validations
     */

    public function editCart() {
        try {

            if ($_POST['data'] != '') {

                $array = json_decode($_POST['data'], true);
                if (isset($array['customer_token']) && $array['customer_token'] == '') {
                    return json_encode(array('status' => "failed", 'message' => 'Token not sent', 'data' => ""));
                }
                $val = $this->cart->valToken($array['customer_token']);

                if ($val['token_status'] == 0) {
                    return json_encode(array('status' => "session", 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => ""));
                }
                if ($val['token_status'] == 1) {

                    $product_id['product_id'] = $array['product_id'];

                    $cart_count = $this->cart->editCart($product_id['product_id'], $array['customer_token'], $array['quantity'], $array['le_wh_id'], $array['segment_id']);
                    $data['data'] = $cart_count;

                    return json_encode(array('status' => 'success', 'message' => 'EditCart', 'data' => $data));
                } else {
                    $error = "Please pass required parameters";
                    return json_encode(array('status' => 'failed', 'message' => $error, 'data' => []));
                }
            }
        } catch (Exception $e) {
            $message = "Internal server error";
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Array('status' => "failed", 'message' => $message, 'data' => []);
        }
    }

    /*
     * Class Name: CheckCartInventory
     * Description: The function is used to InventoryCheck
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 14 Sept 2016
     * Modified Date & Reason: 

     */

    public function CheckCartInventory($cartData = '') {
        try {
            if($cartData != ''){
                $_POST['data'] = $cartData;
            }
            if(isset($_POST['data'])) {
                $array = json_decode($_POST['data'], true);
            } else {
                return json_encode(array('status' => "failed", 'message' => 'Invalid Data Sent', 'data' => ""));
            }

            $request['parameters'] = $array;
            $request['apiUrl'] = 'CheckCartInventory';
            $this->_containerapi = new ContainerapiModel();
            $this->_containerapi->logApiRequests($request);
            $salesUserId = isset($array['sales_rep_id'])?$array['sales_rep_id']:0;
            if (isset($array['customer_token']) && $array['customer_token'] == '') {
                return json_encode(array('status' => "failed", 'message' => 'Token not sent', 'data' => ""));
            }
            $val = $this->cart->valToken($array['customer_token']);
            // Log::info($val);
            if ($val['token_status'] == 0) {
                return json_encode(array('status' => "session", 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => ""));
            }
            // If the order is placed by PO on web, then we don`t validate the Data,
            // But if the order is from Mobile, then we will validate DC and Hub.
            if(!isset($array['is_web'])){
                try {
                    // Here we check the DC and Hub Mapping Relation
                    $result = $this->homepage->checkValidRelation($salesUserId,$array['customer_legal_entity_id']);
                    // If the order is placed by retailer (self login)
                    if($result["status"] == "success"){
                        if($salesUserId == ""){
                            // Then we compare the dc and hub ids to validate
                            if($array['le_wh_id'] != $result["data"][0]->dc_id or $array['hub'] != $result["data"][0]->hub_id)
                                return ['status' => "failed", 'message' => 'Please select a valid DC, Hub', 'data' => $result];
                        }
                    }else{
                        // Failed Validation
                        return $result;
                    }
                } catch (\Exception $e) {
                        return ['status' => "failed", 'message' => 'Improper DC, Hub and Pincode Values', 'data' => "Internal server error"];
                }
                $this->_autoModel = new AutoAssignPickerCheckerModel();
                $dchubmap = $this->_autoModel->checkDCHubMapping($array['le_wh_id'],$array['hub']);
                if(count($dchubmap) == 0){
                    return ['status' => "failed", 'message' => 'Improper DC, Hub mapping!', 'data' => []];
                }
            }
            $cust_id = isset($val['user_id'])?$val['user_id']:0;
            $deleteCartArr = ['customer_token' => $array['customer_token'], 'isClearCart' => 'true'];
            $deletecart = $this->cart->deletecart($deleteCartArr);
            $products_array_size = sizeof($array['products']);
            $customerId = $cust_id; //$this->cart->getcustomerId($array['customer_token']);
            $sales_token = (isset($array['sales_token'])) ? $array['sales_token'] : '';
            $customer_type = (isset($array['customer_type'])) ? $array['customer_type'] : 'NULL';
            // if($customer_type=='3015'){
            //     $array['hub']=env('CNC_HUB_ID');//10694;
            // }else if($customer_type=='3016'){
            //     $array['hub']=env('CLEARANCE_HUB_ID');
            // }
            $mfctype = isset($array['type']) ? $array['type'] : '';

            if($mfctype=='mfc') {

                $order_total = 0;

                foreach($array['products'] as $product) {
                    $order_total+=$product['total_price'];
                    $credit_limit=$product['credit_limit'];
                }             

                if( $order_total  > $credit_limit ){

                    $diff = $order_total - $credit_limit;

                    return json_encode(array('status' => "failed", 'message' => "Order value exceeded credit limit by "  . $diff, 'data' =>$diff));

                }
            }

            $inventory = $this->cart->CheckCartInventory($array['products'], $array['le_wh_id'], $array['hub'], $array['segment_id'], $array['customer_token'], $customerId, $sales_token,$customer_type,$mfctype,$salesUserId);
                           return json_encode(array('status' => "success", 'message' => "CheckCartInventory","total_point"=>$inventory['total_points'],"Color_code"=>$inventory['color_Code'],"ff_total_points"=>$inventory['total_ff_points'],"ff_remaining_points"=>$inventory['remaining_point'],'data' => $inventory['inventoryArray']));

            
            
        } catch (Exception $e) {
            $message = "Internal server error";
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Array('status' => "failed", 'message' => $message, 'data' => []);
        }
    }

    /*
     * Class Name: addcart1
     * Description: The function is used to InventoryCheck
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 14 Sept 2016
     * Modified Date & Reason: 
     */

    public function addcart1() {
        try {
            $array = json_decode($_POST['data'], true);
            $request['parameters'] = $array;
            $request['apiUrl'] = 'addcart1';
            $this->_containerapi = new ContainerapiModel();
            $this->_containerapi->logApiRequests($request);

            if (isset($array['customer_token']) && $array['customer_token'] == '') {
                return json_encode(array('status' => "failed", 'message' => 'Token not sent', 'data' => ""));
            }
            $val = $this->cart->valToken($array['customer_token']);
            if ($val['token_status'] == 0) {
                return json_encode(array('status' => "session", 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => ""));
            }
            $cust_id = isset($val['user_id'])?$val['user_id']:0;
            $is_slab = (isset($array['is_slab']) && $array['is_slab'] != '') ? $array['is_slab'] : 0;
            $customer_id = (isset($array['customer_id']) && $array['customer_id'] != '') ? $array['customer_id'] : $cust_id;
            $customer_type = (isset($array['customer_type']) && $array['customer_type'] != '') ? $array['customer_type'] : 0;
            if ($customer_type == 0) {
                return json_encode(array('status' => "session", 'message' => 'Customer type should not be empty', 'data' => ""));
            }
            $prmt_det_id = (isset($array['prmt_det_id']) && $array['prmt_det_id'] != '') ? $array['prmt_det_id'] : 0;
            $lock_qty = (isset($array['lock_qty']) && $array['lock_qty'] != '') ? $array['lock_qty'] : 0;
            $cust_data = ['customer_id'=>$customer_id,'customer_type'=>$customer_type];
            $inventory = $this->cart->addcart1($array['product_id'], $array['quantity'], $array['le_wh_id'], $array['segment_id'], $cust_data);
            if ($inventory['status'] == -1) {
                return json_encode(array('status' => "failed", 'message' => "Offer is not valid for this quantity", 'data' => $inventory));
            } else {
                return json_encode(array('status' => "success", 'message' => "addcart1", 'data' => $inventory));
            }
        } catch (Exception $e) {
            $message = "Internal server error";
            Log::info("Internal server error" . ' => ' . $e->getTraceAsString());
            return Array('status' => "failed", 'message' => $message, 'data' => []);
        }
    }

    /*
     * Class Name: sendEmailtoFF
     * Description: The function is used to InventoryCheck
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 14 Sept 2016
     * Modified Date & Reason: 
     */

    public function sendEmailtoFF() {
        try {
            $array = json_decode($_POST['data'], true);
            if ((isset($array['customer_token']) && $array['customer_token'] == '') || !isset($array['customer_token'])) {
                return json_encode(array('status' => "failed", 'message' => 'Token not sent', 'data' => ""));
            }
            $val = $this->cart->valToken($array['customer_token']);
            if ($val['token_status'] == 0) {
                return json_encode(array('status' => "failed", 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => ""));
            }
            $customer_id = $array['customer_id'];
            $catObj = new \App\Modules\Cpmanager\Models\CategoryModel();
            $user_data = $catObj->getUserId($array['customer_token']);
            $user_id = isset($user_data[0]->user_id) ? $user_data[0]->user_id : '';
            if ($user_id == '' || $customer_id == '') {
                return json_encode(array('status' => "failed", 'message' => 'User id or Customer id should not be empty', 'data' => ""));
            }
            $retailerInfo = $this->cart->getRetailerInfo($customer_id);
            $instance = env('MAIL_ENV');
            $subject = $instance . 'New Retailer without Beat';
            $body['attachment'] = '';
            $body['file_name'] = '';
            $body['template'] = 'emails.po';
            $body['name'] = 'Hello';
            $body['comment'] = '<p>New customer <strong>' . $retailerInfo->business_legal_name . ' '.'('.$retailerInfo->mobile_no.') ' . 'Address: ' . $retailerInfo->address1 . ', ' . $retailerInfo->address2 . ', ' . $retailerInfo->locality . ', ' . $retailerInfo->city . ', ' . $retailerInfo->state_name . ' - ' . $retailerInfo->pincode . '</strong> onboarded please do the needful & Map the customer to the Respective Beat.</p>';
            $toEmails = array();
            
            $notificationObj= new NotificationsModel();
            $userIdData= $notificationObj->getUsersByCode('BEAT001');
            $userIdData=json_decode(json_encode($userIdData),true);
            $purchaseOrder = new PurchaseOrder();
            $userEmailArr = $purchaseOrder->getUserEmailByIds($userIdData);
            if(is_array($userEmailArr) && count($userEmailArr) > 0) {
                foreach($userEmailArr as $userData){
                    $toEmails[] = $userData['email_id'];
                }
            }
            \Utility::sendEmail($toEmails, $subject, $body);
            return json_encode(array('status' => "success", 'message' => 'Email sent successfully', 'data' => ""));
        } catch (Exception $e) {
            $message = "Internal server error";
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Array('status' => "failed", 'message' => $message, 'data' => []);
        }
    }

    /*
     * Class Name: updateBeat
     * Description: The function is used to InventoryCheck
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 14 Sept 2016
     * Modified Date & Reason: 
     */

    //to update Beat Id for Customer
    public function updateBeat() {
        try {
            $array = json_decode($_POST['data'], true);
            if ((isset($array['customer_token']) && $array['customer_token'] == '') || !isset($array['customer_token'])) {
                return json_encode(array('status' => "failed", 'message' => 'Token not sent', 'data' => ""));
            }
            $val = $this->cart->valToken($array['customer_token']);
            if ($val['token_status'] == 0) {
                return json_encode(array('status' => "session", 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => ""));
            }
            $customer_id = $array['customer_id'];
            $beat_id = $array['beat_id'];
            if ($beat_id == '' || $customer_id == '' || $beat_id <= 0) {
                return json_encode(array('status' => "failed", 'message' => 'Beat id or Customer id should not be empty', 'data' => ""));
            }
            $catObj = new \App\Modules\Cpmanager\Models\CategoryModel();
            $user_data = $catObj->getUserId($array['customer_token']);
            $userId = isset($user_data[0]->user_id) ? $user_data[0]->user_id : 0;
            $registration_obj = new \App\Modules\Cpmanager\Models\RegistrationModel();
            $hub_id = $registration_obj->getHub($beat_id);
            if (empty($hub_id) || $hub_id <= 0) {
                return json_encode(array('status' => "failed", 'message' => 'Hub id should not be empty', 'data' => ""));
            }
            $le_wh_id = $registration_obj->getWarehouseidByHub($hub_id);
            if (empty($le_wh_id) || $le_wh_id == '') {
                return json_encode(array('status' => "failed", 'message' => 'warehouse id should not be empty', 'data' => ""));
            }
            $this->cart->updateBeat($userId, $customer_id, $beat_id, $le_wh_id, $hub_id);
            $data['hub_id'] = $hub_id;
            $data['le_wh_id'] = $le_wh_id;
            return json_encode(array('status' => "success", 'message' => 'Beat updated successfully', 'data' => $data));
        } catch (Exception $e) {
            $message = "Internal server error";
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Array('status' => "failed", 'message' => $message, 'data' => []);
        }
    }

    public function flushPriceCache($productId) {
        try {
            if ($productId > 0) {
                $appKeyData = env('DB_DATABASE');
                // This doesn`t work
                $keyString = $appKeyData . '_product_slab_' . $productId.'_le_wh_id_'.$le_wh_id;
                Cache::forget($keyString);
//                Caching::flush($productId);
            } else {
                echo "No Product Id";
            }
        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
            echo "<pre>";
            print_r($ex->getTraceAsString());
            die;
        }
    }

    public function flushCache($key) {
        try {
            if ($key != '') {
                $appKeyData = env('DB_DATABASE');
                $keyString = $appKeyData . $key;
                Cache::forget($keyString);
//                Caching::flush($productId);
            } else {
                echo "No Product Id";
            }
        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
            echo "<pre>";
            print_r($ex->getTraceAsString());
            die;
        }
    }

    public function flushPriceCacheByOrderId($orderId) {
        try {
            if ($orderId > 0) {
                $this->cart->flushPriceCacheByOrderId($orderId);
            } else {
                echo "No order id";
            }
        } catch (\ErrorException $ex) {
            echo $ex->getMessage();
            echo "<pre>";
            print_r($ex->getTraceAsString());
            die;
        }
    }
}
