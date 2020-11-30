<?php

namespace App\Modules\Cpmanager\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;
use Log;
use Mail;
use Cache;
use Caching;
use App\Modules\Cpmanager\Models\EcashModel;
use App\Modules\Cpmanager\Controllers\accountController;


class CartModel extends Model {

    public function __construct() {
        $this->Review = new Review();
        $this->_ecash = new EcashModel();
    }

    /*
     * Function name: valAppidToken
     * Description: used to validate Appid & customer id details
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 1st July 2016
     * Modified Date & Reason:
     */

    public function valToken($token) {
        try {
            $data['token_status'] = 0;
            $account = new accountController();
            $result1=$account->getDataFromToken(1,$token,(DB::raw('*')));

            if (count($result1) > 0){
                $data['token_status'] = 1;
                $data['user_id'] = isset($result1->user_id)?$result1->user_id:0;
            }
            return $data;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * Function name: getcustomerId
     * Description: based on  customer token, we are getting customer id 
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 7th July 2016
     * Modified Date & Reason:
     */

    public function getcustomerId($token) {
        try {
            $result = DB::table('users')
                    ->select(DB::raw('user_id'))
                    ->where('password_token', '=', $token)
                    ->useWritePdo()
                    ->get()->all();

            if (empty($result)) {
                $customerId = 0;
            } else {
                $data = json_decode(json_encode($result[0]), true);
                $customerId = $data['user_id'];
            }
            return $customerId;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
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

    public function addtocart($variantarray, $token, $customerId) {
        try {
            $sizeof_product_array = sizeof($variantarray);
            $cartArray = array();
            $cartArray['cart'] = array();
            for ($i = 0; $i < $sizeof_product_array; $i++) {
                $esu_qty = (isset($variantarray[$i]['esu_quantity']) && $variantarray[$i]['esu_quantity'] != '') ? $variantarray[$i]['esu_quantity'] : '';
                $produc_ID = $variantarray[$i]['variant_id'];
                //$esu_qty = $variantarray[$i]['esu_quantity'];
                $total_qty = $variantarray[$i]['total_qty'];
                $total_price = $variantarray[$i]['total_price'];
                $rate = $variantarray[$i]['unit_price'];
                $margin = $variantarray[$i]['applied_margin'];

                $check_cart_table = DB::table('cart')
                        ->select(DB::raw('count(cart_id) as cc,cart_id'))
                        ->where('product_id', '=', $produc_ID)
                        ->where('user_id', '=', $customerId)
                        ->get()->all();
                $cart_table = json_decode(json_encode($check_cart_table[0]), true);
                $cart_table_count = $cart_table['cc'];
                if ($cart_table_count == 0) {
                    $insert_product_id = DB::table('cart')
                            ->insert(['product_id' => $produc_ID,
                        'user_id' => ".$customerId.",
                        'session_id' => $token,
                        //'esu_quantity'=>$esu_qty,
                        'quantity' => $total_qty,
                        'total_price' => $total_price,
                        'rate' => $rate,
                        'margin' => $margin,
                        //'le_wh_id_list'=>"$le_wh_id",
                        'created_at' => date("Y-m-d H:i:s")
                    ]);
                    $CART_ID = DB::getPdo()->lastInsertId();
                } else {

                    $update_cart_table = DB::Table('cart')
                            ->where('user_id', $customerId)
                            ->where('product_id', $produc_ID)
                            ->update(array('quantity' => $total_qty, 'total_price' => $total_price, 'rate' => $rate, 'margin' => $margin, 'updated_at' => date("Y-m-d H:i:s")));

                    $CART_ID = $cart_table['cart_id'];
                }
                $cart_count = $this->add_cartcount($customerId);
                $cartArray['status'] = "added to cart successfully done";
                $cartArray['cartcount'] = $cart_count;
                $cartArray['cart'][$i] = $CART_ID;
            }
            return $cartArray;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * Classme: valCart
     * Description: function is used to validate cart
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 9th Aug 2016
     * Modified Date & Reason: 
      added validations
     */

    public function valCart($data) {
        try {
            $check_prod = DB::table('cart as c')
                    ->select(DB::raw('c.cart_id'))
                    ->where('c.product_id', '=', $data['product_id'])
                    ->where('c.cart_id', '=', $data['cartId'])
                    ->get()->all();

            if (empty($check_prod)) {
                $cartcount = 0;
            } else {
                $data = json_decode(json_encode($check_prod), true);
                $cartcount = sizeof($data);
            }
            return $cartcount;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * Class Name: checkInventory
     * Description: Checking quantity of product 
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 8th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function checkInventory($product_id, $le_wh, $segmentId, $total_qty) {

        $warehouseId = DB::select("SELECT GetCPInventoryStatus($product_id,'" . $le_wh . "',$segmentId,4) as le_wh_id ");
        $le_wh_id = $warehouseId[0]->le_wh_id;
        if (($le_wh_id == 0) || $warehouseId = '') {
            return json_encode(array('status' => "failed", 'message' => "Out of stock", 'data' => []));
        } else {
            $checkInventory = DB::table('inventory')
                    ->select(DB::raw('inv_display_mode'))
                    ->where('product_id', '=', $product_id)
                    ->where('le_wh_id', '=', $le_wh_id)
                    ->get()->all();
            $displaymode = $checkInventory[0]->inv_display_mode;
            if ($displaymode == 'soh') {
                $query = DB::table('inventory')
                        ->select(DB::raw('(soh-order_qty) as availQty'))
                        ->where('product_id', '=', $product_id)
                        ->where('le_wh_id', '=', $le_wh_id)
                        ->get()->all();
            } else if ($displaymode == 'atp') {
                $query = DB::table('inventory')
                        ->select(DB::raw('(atp-order_qty) as availQty'))
                        ->where('product_id', '=', $product_id)
                        ->where('le_wh_id', '=', $le_wh_id)
                        ->get()->all();
            } else {
                $query = DB::table('inventory')
                        ->select(DB::raw('((soh+atp)-order_qty) as availQty'))
                        ->where('product_id', '=', $product_id)
                        ->where('le_wh_id', '=', $le_wh_id)
                        ->get()->all();
            }
            $avail_quantity = $query[0]->availQty;
            if (($total_qty) > $avail_quantity) {
                return json_encode(array('status' => "failed", 'message' => "Ordered quantity isn't available but you can place order for " . $avail_quantity . " quantity", 'data' => []));
            }
        }
    }

    /*
     * Class Name: deletecart
     * Description: Delete product from cart
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 8th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function deletecart($data) {
        $user_id = $this->getcustomerId($data['customer_token']);
        if ($data['isClearCart'] == 'true') {
            DB::table('cart')
                    ->where('user_id', $user_id)
                    ->where('status', 1)
                    ->useWritePdo()
                    ->delete();
            DB::table('cart_product_packs')
                    ->where('user_id', $user_id)
                    ->where('status', 1)
                    ->useWritePdo()
                    ->delete();
        } else {

            DB::table('cart')->where('cart_id', $data['cartId'])
                    ->useWritePdo()
                    ->delete();
            DB::table('cart_product_packs')->where('cart_id', $data['cartId'])
                    ->useWritePdo()
                    ->delete();
        }
    }

    /*
     * Class Name: getViewcartData
     * Description: View  product of cart
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 11th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function getViewcartData($customerId) {

        $query = DB::select(DB::raw('select oc.cart_id as cartId,p.product_title as Name,p.product_id,p.product_id as variant_id,oc.total_price,oc.created_at  from cart oc 
      left join products p on p.product_id=oc.product_id       
      where user_id=' . $customerId . ' order by p.is_parent=1 and p.is_active=1 desc'));

        $cart_details = json_decode(json_encode($query), true);

        return $cart_details;
    }

    /*
     * Class Name: variant
     * Description: funtion used to Get cart ,Product & variant details 
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 11th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function variant($prod_id, $customer_id) {
        try {
            $query = DB::select(DB::raw('select p.product_title as product_name,pc.description,p.product_id as product_variant_id,p.product_id as variant_id,cp.variant_value1 as name,cp.primary_image as Image,p.sku,p.mrp,p.is_parent as is_default,oc.quantity as Total_quantity,oc.total_price as Total_Price,oc.rate as applied_mrp,oc.margin as applied_margin  from cart oc 
    left join products p on p.product_id= oc.product_id
    left join vw_cp_products cp on cp.product_id=oc.product_id
    left join product_content pc on pc.product_id=oc.product_id
    where oc.product_id=' . $prod_id . ' and oc.user_id=' . $customer_id . ' group by p.product_id order by oc.cart_id ASC'));
            $pack_details = json_decode(json_encode($query), true);
            return $pack_details;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * Class Name: getPackdata
     * Description: funtion used to get margin
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 12th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function getPackdata($product_id, $token, $le_wh_id) {
        try {
            //Log::info(__METHOD__);
            $user_id = $this->getcustomerId($token);
            // $getPackdata = DB::select("CALL getProductSlabs($product_id,'".$le_wh_id."',$user_id)");  
            $temp = trim($le_wh_id, "'");
            $temp = str_replace(',', '_', $temp);
//                $keyString = 'product_slab_'.$product_id.'_'.$temp;
            $appKeyData = env('DB_DATABASE');
            $keyString = $appKeyData . '_product_slab_' . $product_id.'_le_wh_id_'.trim($le_wh_id,"'");
            //Log::info('keyString');
            //Log::info($keyString);
            $getPackdata = [];
            $response = Cache::get($keyString);
            if ($response != '') {
                $slabDetails = json_decode($response, true);
                if (isset($slabDetails[$temp])) {
                    //Log::info('We have this in Cache for product id: '.$product_id.' request from the user Id '.$user_id);
                    $getPackdata = $slabDetails[$temp];
                } else {
                    //Log::info('We do not have this in Cache for product id: '.$product_id.' request from the user Id '.$user_id);
                    $getPackdata = DB::select("CALL getProductSlabs($product_id,'" . $le_wh_id . "',$user_id)");
                    $slabDetails[$temp] = $getPackdata;
                    Cache::put($keyString, json_encode($slabDetails), 60);
                }
            } else {
                //Log::info('We do not have this in Cache for product id: '.$product_id.' request from the user Id '.$user_id);
                $getPackdata = DB::select("CALL getProductSlabs($product_id,'" . $le_wh_id . "',$user_id)");
                $slabDetails[$temp] = $getPackdata;
                Cache::put($keyString, json_encode($slabDetails), 60);
            }
            return json_decode(json_encode($getPackdata), true);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * Class Name: add_cartcount
     * Description: Count of products in after adding or updating cart
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 13th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function add_cartcount($customer_id) {
        try {
            $query = DB::select(DB::raw("select count(cart_id) as cc from cart where user_id='" . $customer_id . "'"));
            if (empty($query)) {
                $count = 0;
            } else {
                $total = json_decode(json_encode($query[0]), true);
                $count = $total['cc'];
            }
            return $count;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * Class Name: cartcount
     * Description: Count of products in cart after placing order
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 13th July 2016
     * Modified Date & Reason: 
      added validations
     */

    public function cartcount($customer_id) {
        try {
            $query = DB::select(DB::raw("select count(cart_id) as cc from cart where user_id='" . $customer_id . "' and status='0'"));
            $cartcount = $query[0]->cc;
            if ($cartcount > 0) {
                $count = 0;
            } else {
                $query = DB::select(DB::raw("select count(cart_id) as cc from cart where user_id='" . $customer_id . "'"));
                $total = json_decode(json_encode($query[0]), true);
                $count = $total['cc'];
            }
            return $count;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * Class Name: getPackPrice
     * Description: The function is used to InventoryCheck
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 12 Sept 2016
     * Modified Date & Reason: 

     */

    function getPackPrice($qty, $packSizeArr) {
        try {
            if (isset($packSizeArr[$qty])) {
                return $packSizeArr[$qty];
            } else {
                krsort($packSizeArr);
                foreach ($packSizeArr as $packSize => $packPrice) {
                    if ($qty > $packSize) {
                        return $packSizeArr[$packSize];
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * Class Name: CheckCartInventory
     * Description: The function is used to InventoryCheck
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 12 Sept 2016
     * Modified Date & Reason: 
     */
    public function CheckCartInventory($cartdata, $wh_id, $hub, $segmentId, $token, $customerId, $sales_token = '', $cust_type,$mfctype ='',$salesUserId) {
        try {
            $sizeof_cart = sizeof($cartdata);
            $total_points = 0;
            $legal_entity_id = $this->_ecash->getLegalEntityId($customerId);
            if ($cust_type == 'NULL') {
                $customer_type = $this->_ecash->getUserCustomerType($legal_entity_id);
            } else {
                $customer_type = $cust_type;
            }
            $products_free_notavail = $this->checkFreeProds($cartdata, $wh_id, $segmentId, $token, $customerId,$customer_type);
            $removedPr = array();
            for ($i = 0; $i < $sizeof_cart; $i++) {
                $esu_quantity = (isset($cartdata[$i]['esu_quantity']) && $cartdata[$i]['esu_quantity'] != '') ? $cartdata[$i]['esu_quantity'] : '';
                $margin = (isset($cartdata[$i]['applied_margin']) && $cartdata[$i]['applied_margin'] != '') ? $cartdata[$i]['applied_margin'] : '';
                $total_price = floatval((isset($cartdata[$i]['total_price']) && $cartdata[$i]['total_price'] != '') ? $cartdata[$i]['total_price'] : 0.00);
                $total_qty = (isset($cartdata[$i]['total_qty']) && $cartdata[$i]['total_qty'] != '') ? $cartdata[$i]['total_qty'] : '';
                $rate = (isset($cartdata[$i]['unit_price']) && $cartdata[$i]['unit_price'] != '') ? $cartdata[$i]['unit_price'] : '';
                $product_id = $cartdata[$i]['product_id'];
                $parent_id = $cartdata[$i]['parent_id'];
                $quantity = $cartdata[$i]['total_qty'];
                $warehouseId = DB::select("SELECT GetCPInventoryStatus(" . $product_id . ",'" . $wh_id . "',$segmentId,4) as le_wh_id ");
                $le_wh_id = $warehouseId[0]->le_wh_id;
                $is_slab = (isset($cartdata[$i]['is_slab']) && $cartdata[$i]['is_slab'] != '') ? $cartdata[$i]['is_slab'] : 0;
                $star = (isset($cartdata[$i]['star']) && $cartdata[$i]['star'] != '') ? $cartdata[$i]['star'] : '';
                $product_slab_id = (isset($cartdata[$i]['product_slab_id']) && $cartdata[$i]['product_slab_id'] != '') ? $cartdata[$i]['product_slab_id'] : 0;
                $prmt_det_id = (isset($cartdata[$i]['prmt_det_id']) && $cartdata[$i]['prmt_det_id'] != '') ? $cartdata[$i]['prmt_det_id'] : 0;
                $freebee_mpq = (isset($cartdata[$i]['freebee_mpq'])) ? $cartdata[$i]['freebee_mpq'] : 0;
                $freebee_qty = (isset($cartdata[$i]['freebee_qty'])) ? $cartdata[$i]['freebee_qty'] : 0.0;
                $esu = (isset($cartdata[$i]['esu']) && $cartdata[$i]['esu'] != '') ? $cartdata[$i]['esu'] : 0;
                $packs = (isset($cartdata[$i]['packs']) && $cartdata[$i]['packs'] != '') ? $cartdata[$i]['packs'] : [];
                $sku = (isset($cartdata[$i]['sku']) && $cartdata[$i]['sku'] != '') ? $cartdata[$i]['sku'] : "";
                $product_points =  (isset($cartdata[$i]['product_point']) && $cartdata[$i]['product_point'] != '') ? $cartdata[$i]['product_point']:0;
                $total_points =  $total_points + ($total_qty * $product_points);
                Log::info($total_points);
                //print_r("$total_points", $total_points);
                $credit_limit = isset($cartdata[$i]['credit_limit']) ? $cartdata[$i]['credit_limit'] : 0;

                // checking the bfil condition here

                if (!empty($star)) {
                    $star = $this->getMastValue($cartdata[$i]['star']);
                }
                $hub = (!empty($hub) && isset($hub)) ? $hub : 0;
                /* packs validation */
                if (empty($packs)) {
                    $data['status'] = -5;
                    $data['cartId'] = '';
                    $data['product_id'] = $product_id;
                    $data['le_wh_id'] = $wh_id;
                    $data['hub_id'] = $hub;
                    $data['available_quantity'] = 0;
                    $removedPr[] = $parent_id;
                }
                /* packs validation end */

                /////////////////////////////////////////////////////////////////////////////////
                //// Price Validations BEGIN
                $wrong_unit_price = false;
                $isFreebie = 0;
                $isFreebie = $this->isFreebie($product_id);

                // check free qty or free sample
                if(!$isFreebie)
                    $isFreebie = $this->isFreeQty($product_id);

                //////////////////////////////////////////////////////////////
                /////////Start Discount Validation
                # Discount only for self orders
                $isDiscountValid = true;
                $isDiscountApplicable = false;
                if ($sales_token == '' and ! $isFreebie) {
                    $discount = (isset($cartdata[$i]["discount"]) and $cartdata[$i]["discount"] != '') ? $cartdata[$i]["discount"] : 0;
                    $discountType = (isset($cartdata[$i]["discount_type"]) and $cartdata[$i]["discount_type"] != '') ? $cartdata[$i]["discount_type"] : '';
                    $discountOn = (isset($cartdata[$i]["discount_on"]) and $cartdata[$i]["discount_on"] != '') ? $cartdata[$i]["discount_on"] : '';
                    $discountOnValues = (isset($cartdata[$i]["discount_on_values"]) and $cartdata[$i]["discount_on_values"] != '') ? $cartdata[$i]["discount_on_values"] : '';
                    $discountAmount = 0;

                    $isDiscount = 0;
                    if ($discount == 0 and $discountType == '' and $discountOn == '' and $discountOnValues == '')
                        $isDiscountApplicable = false;
                    else {
                        $today = date("Y-m-d H:i:s");
                        #Query to Check the Validty of the Discount
                        $isDiscount = DB::table('customer_discounts')
                                ->select('discount')
                                ->where([
                                    ['discount_on', '=', $discountOn],
                                    ['discount_type', '=', $discountType],
                                    ['discount_on_values', 'LIKE', '%' . $discountOnValues . '%'],
                                    ['discount_start_date', '<=', $today],
                                    ['discount_end_date', '>=', $today],
                                ])
                                ->first();
                        if (isset($isDiscount->discount) and ( $isDiscount->discount != $discount)) {
                            # Status 4 is for In Valid Discount
                            $isDiscountValid = false;
                            $data['status'] = -4;
                            $data['cartId'] = '';
                            $data['product_id'] = $product_id;
                            $data['le_wh_id'] = $wh_id;
                            $data['hub_id'] = $hub;
                            $data['available_quantity'] = 0;
                            $removedPr[] = $product_id;
                        }
                        // Log::info(DB::getQueryLog());

                        if ($isDiscountValid) {
                            $isDiscountApplicable = true;
                            # if there is match
                            if ($discountType == "percentage")
                                $discountAmount = $discount * floatval($total_price) / 100;
                            elseif (($discountType == "value") and floatval($total_price) > $discount)
                                $discountAmount = $discount;
                            // $discountAmount = floatval($total_price) - $discount;
                            $discountAmount = ($discountAmount < 0) ? 0 : $discountAmount;
                            // The Below is for Future
                            // $discountAmount = $this->calculateDiscount($discount,$discountType,$total_price);
                        }
                        else {
                            $isDiscountApplicable = false;
                            # As of now Discount is Not Mandatory for all the products in the Cart;	
                        }
                    }
                }
                // End Discount
                //////////////////////////////////////////////////////////////

                if (($le_wh_id == 0) || empty($le_wh_id) || ($wh_id == 0) || empty($wh_id)) {
                    $data['status'] = 0;
                    $data['cartId'] = '';
                    $data['product_id'] = $product_id;
                    $data['le_wh_id'] = $le_wh_id;
                    $data['hub_id'] = $hub;
                    $data['available_quantity'] = 0;
                    $this->inventoryRequest($product_id, $le_wh_id, $segmentId, $quantity, $customerId);
                } else {

                    if ($customer_type == 3016) {
                        /*$query = DB::table('inventory')
                                ->select(DB::raw('(dit_qty-(dit_order_qty+dit_reserved_qty)) as availQty'))
                                ->where('product_id', '=', $product_id)
                                ->where('le_wh_id', '=', $le_wh_id)
                                ->get()->all();*/
                        $query = DB::selectFromWriteConnection(DB::raw("select (dit_qty-(dit_order_qty+dit_reserved_qty)) as availQty from `inventory` where `product_id` = $product_id and `le_wh_id` = $le_wh_id"));
                    } else {
                        $checkInventory = DB::table('inventory')
                                ->select(DB::raw('inv_display_mode'))
                                ->where('product_id', '=', $product_id)
                                ->where('le_wh_id', '=', $le_wh_id)
                                ->get()->all();
                        $displaymode = $checkInventory[0]->inv_display_mode;
                       /*$query = DB::table('inventory')
                                ->select(DB::raw('(' . $displaymode . '-(order_qty+reserved_qty)) as availQty'))
                                ->where('product_id', '=', $product_id)
                                ->where('le_wh_id', '=', $le_wh_id)
                                ->get()->all();*/
                        $query = DB::selectFromWriteConnection(DB::raw("select ($displaymode-(order_qty+reserved_qty)) as availQty from `inventory` where `product_id` = $product_id and `le_wh_id` = $le_wh_id"));
                    }
                    $avail_quantity = $query[0]->availQty;
                    if (($quantity) > $avail_quantity || in_array($product_id, $products_free_notavail) || in_array($parent_id, $products_free_notavail)) {

                        $data['status'] = 0;
                        $data['cartId'] = '';
                        $data['product_id'] = $product_id;
                        $data['le_wh_id'] = $wh_id;
                        $data['hub_id'] = $hub;
                        $data['available_quantity'] = $avail_quantity;
                        $this->inventoryRequest($product_id, $wh_id, $segmentId, $quantity, $customerId);
                    } else {
                        $CheckUnitPrice = array();

                        if ($product_id == $parent_id || $parent_id == 0) {
                            $appKeyData = env('DB_DATABASE');
                            $temp = trim($wh_id, "'");
                            $temp = str_replace(',', '_', $temp);
                            if ($customerId == 0) {
                                $temp = 0;
                            }
                            $keyString = $appKeyData . '_product_slab_' . $product_id . '_customer_type_' . $customer_type.'_le_wh_id_'.$le_wh_id;
                            // Log::info($keyString);
                            $response = Cache::get($keyString);
                            $response='';
                            $unitPriceData = ($response != '') ? (json_decode($response, true)) : [];
                            if (isset($unitPriceData[$temp])) {
                                $CheckUnitPrice = $unitPriceData[$temp];
                                $tempDetails = [];
                                if (isset($avail_quantity)) {
                                    foreach ($CheckUnitPrice as $slabData) {
                                        if (isset($slabData['stock'])) {
                                            $slabData['stock'] = $avail_quantity;
                                        }
                                        $tempDetails[] = $slabData;
                                    }
                                }
                                if (!empty($tempDetails)) {
                                    $CheckUnitPrice = $tempDetails;
                                }
                                $unitPriceData[$temp] = json_decode(json_encode($CheckUnitPrice), true);
                                Cache::put($keyString, json_encode($unitPriceData), 60);
                            } else {
                                // Log::info('We do not have this in Cache for product id: '.$product_id.' request from the user Id '.$customerId);
                                //$CheckUnitPrice = DB::select("CALL  getProductSlabs(" . $product_id . ",'" . $wh_id . "'," . $customerId . ")");
                                $CheckUnitPrice = DB::selectFromWriteConnection(DB::raw("CALL getProductSlabsByCust($product_id,'" . $wh_id . "',$customerId,$customer_type)"));
                                $unitPriceData[$temp] = json_decode(json_encode($CheckUnitPrice), true);
                                Cache::put($keyString, json_encode($unitPriceData), 60);
                            }
                        }
                        // Log::info('CheckUnitPrice');
                        // Log::info($CheckUnitPrice);
                        $packSizeArr = array();
                        foreach ($CheckUnitPrice as $price) {
                            if (is_array($price)) {
                                //Log::info('This is array');
                                $packSizeArr[$price['pack_size']] = $price['unit_price'];
                            } elseif (is_object($price)) {
                                //Log::info('This is object');
                                $packSizeArr[$price->pack_size] = $price->unit_price;
                            }
                        }
                        $packSizePrice = $this->getPackPrice($quantity, $packSizeArr);
                        //Log::info('cart pack price');
                        //Log::info($packSizePrice);
                         //Log::info($rate);
                        if ((!$isFreebie && count($CheckUnitPrice) == 0) || (isset($packSizePrice) && $packSizePrice != $rate)) {
                            $data['status'] = -1;
                            $data['cartId'] = '';
                            $data['product_id'] = $product_id;
                            $data['le_wh_id'] = $wh_id;
                            $data['hub_id'] = $hub;
                            $data['available_quantity'] = $avail_quantity;
                            $data['old_price'] = $rate;
                            $data['new_price'] = $packSizePrice;
                            $data['old_total_price'] = $rate * $total_qty;
                            $data['new_total_price'] = $packSizePrice * $total_qty;
                            $removedPr[] = $product_id;
                        } else {
                            /**
                             * check cart count based user login
                             */
                            $status = 1;
                            if (in_array($parent_id, $removedPr)) {
                                if ($wrong_unit_price)
                                    $status = -2;
                                else if (!$isDiscountValid)
                                    $status = -4;
                                elseif (empty($packs))
                                    $status = -5;
                                else
                                    $status = -1;

                                $cartId = '';
                                unset($data['old_price']);
                                unset($data['new_price']);
                                unset($data['old_total_price']);
                                unset($data['new_total_price']);
                            }
                            else {
                                // $check_cart_table = DB::table('cart')
                                //         ->select(DB::raw('count(cart_id) as cc,cart_id'))
                                //         ->where('product_id', '=', $product_id)
                                //         ->where('user_id', '=', $customerId)
                                //         ->where('status', '=', 1)
                                //         ->get()->all();
                                $check_cart_table = DB::selectFromWriteConnection(DB::raw("select count(cart_id) as cc,cart_id from `cart` where `product_id` = $product_id and `user_id` = $customerId and `status` = 1"));
                                $whdata = $this->getDCByHub($hub);
                                $lewhid = isset($whdata->dc_id)?$whdata->dc_id:0;
                                $cart_table = json_decode(json_encode($check_cart_table[0]), true);
                                $cart_table_count = $cart_table['cc'];

                                /**
                                 * Cart insertion
                                 */
                                if ($cart_table_count == 0) {
                                    $cartArr = ['product_id' => $product_id,
                                    'user_id' => $customerId,
                                    'session_id' => $token,
                                    'esu_quantity' => $esu_quantity,
                                    'esu' => $esu,
                                    'parent_id' => $parent_id,
                                    'total_price' => $total_price,
                                    'rate' => $rate,
                                    'margin' => $margin,
                                    'le_wh_id_list' => "$wh_id",
                                    "le_wh_id" =>$lewhid,
                                    'hub_id' => $hub,
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'star' => $star,
                                    'is_slab' => $is_slab,
                                    'prmt_det_id' => $prmt_det_id,
                                    'product_slab_id' => $product_slab_id,
                                    'freebee_mpq' => $freebee_mpq,
                                    'freebee_qty' => $freebee_qty,
                                    'discount' => (($sales_token == '') and $isDiscountApplicable and!$isFreebie) ? $discount : 0,
                                    'discount_type' => (($sales_token == '') and $isDiscountApplicable and!$isFreebie) ? $discountType : '',
                                    'discount_on' => (($sales_token == '') and $isDiscountApplicable and!$isFreebie) ? $discountOn : '',
                                    'discount_amount' => (($sales_token == '') and $isDiscountApplicable and!$isFreebie) ? $discountAmount : 0
                                    ];
                                    if ($customer_type == 3016) {
                                        $cartArr['dit_quantity'] = $total_qty;
                                    }else{
                                        $cartArr['quantity'] = $total_qty;
                                    }
                                    $CartInsertion = DB::table('cart')
                                            ->insert($cartArr);

                                    $CART_ID = DB::getPdo()->lastInsertId();

                                    if (!empty($packs)) {
                                        foreach ($packs as $value) {
                                            $pack_cashback = isset($value['pack_cashback']) && !empty($value['pack_cashback']) ? $value['pack_cashback'] : 0;
                                            $pack_qty = isset($value['pack_qty']) && !empty($value['pack_qty']) ? $value['pack_qty'] : 0;
                                            DB::table('cart_product_packs')
                                                    ->insert(['product_id' => $product_id,
                                                        'user_id' => $customerId,
                                                        'cart_id' => $CART_ID,
                                                        'session_id' => $token,
                                                        'esu' => $value['esu'],
                                                        'esu_quantity' => $value['qty'],
                                                        'star' => $value['star'],
                                                        'pack_level' => $value['pack_level'],
                                                        'created_at' => date("Y-m-d H:i:s"),
                                                        'pack_price' => $rate * $pack_qty,
                                                        'pack_qty' => $pack_qty,
                                                        'pack_cashback' => $pack_cashback
                                            ]);
                                        }
                                    }
                                }
                                /**
                                 * Cart Updation
                                 */ else {

                                    Log::info('in else part due to Cart not deleted while checkcart for product id: ' . $product_id . ' request from the user Id ' . $customerId . '_customer_type_' . $customer_type);
                                    $CART_ID = '';
                                }
                                $cartId = $CART_ID;
                            }
                            $data['status'] = $status;
                            $data['cartId'] = $cartId;
                            $data['product_id'] = $product_id;
                            $data['le_wh_id'] = $wh_id;
                            $data['hub_id'] = $hub;
                            $data['available_quantity'] = $avail_quantity;
                        }
                    }
                }
                $inventoryArray[$i] = $data;
            }
            Log::info('------797-----------');
            if($total_points >= 0){
                $color_Code = '#0000FF';
            } else {
                $color_Code = '#FF0000';
            }
            Log::info($total_points);
            Log::info('------804-----------');
            Log::info($color_Code);
            // DB::enableQueryLog();

            //used to get after and end date of current month
            $first_day_this_month = date('y-m-01 00:00:00'); // hard-coded '01' for first day
            $last_day_this_month  = date('Y-m-t 23:59:59');
            $ff_points_Details  = DB::table('ff_targets_cons')
                                ->select(DB::raw('ff_targets_cons.`tbv_tgt` as total_point,ff_targets_cons.`tbv_bal` as balance_point, ff_targets_cons.`tbv_ach` as point_achieve'))
                                ->where('start_date', '=', $first_day_this_month)
                                ->where('end_date', '=', $last_day_this_month)
                                ->where('ff_user_id', '=', $salesUserId)
                                ->get();
            //  print_r(DB::getQueryLog());
            Log::info('------815-----------');
            Log::info($first_day_this_month); 
            Log::info($last_day_this_month);
            Log::info('------818-----------');
            Log::info($salesUserId);
            Log::info($ff_points_Details);
            if(count($ff_points_Details)  > 0)
            {
                Log::info('------826-----------');  
                $total_ff_points =$ff_points_Details[0]->point_achieve  + $total_points;
                $remaining_point =  $ff_points_Details[0]->total_point -  $total_ff_points;  
            }else {
                Log::info('------826-----------');
                $total_ff_points =0.00;
                $remaining_point = 0.00;
            }
           
            Log::info('------818-----------');
            Log::info($total_ff_points);
            Log::info('------820-----------');
            Log::info($remaining_point);
            $finalresponse=array();
            $finalresponse['inventoryArray'] =$inventoryArray;
            $finalresponse['total_points']=$total_points;
            $finalresponse['color_Code']= $color_Code;
            $finalresponse['total_ff_points']= $total_ff_points;
            $finalresponse['remaining_point']= $remaining_point;
            return $finalresponse;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }



    public function getDCByHub($hub_id) {
        $data = DB::table('dc_hub_mapping')
                        ->select('dc_hub_map_id','dc_id', 'is_active')
                        ->where('hub_id', $hub_id)
                        ->first();
        return $data;
    }
    /*
     * Class Name: isFreebie
     * Description: Code to check weather the product is a freebie or not
     * Author: Ebutor <arjun.pydi@ebutor.com>
     * Copyright: ebutor 2017
     * Version: v1.0
     * Created Date: 4 July 2017
     * Modified Date & Reason: 
     */

    public function isFreebie($product_id = null) {
        $result = false;
        if ($product_id != null or $product_id != '') {
            $result = DB::table('freebee_conf')
                    ->where('free_prd_id', $product_id)
                    ->count();
            if ($result > 0)
                $result = true;
        }
        return $result;
    }

    /*
     * Class Name: isFreeqty or freesample
     * Description: Code to check weather the product is a freeqty/freesample or not
     * Copyright: ebutor 2017
     * Version: v1.0
     * Created Date: 4 July 2017
     * Modified Date & Reason: 
     */

    public function isFreeQty($product_id = null) {
        $result = false;
        if ($product_id != null or $product_id != ''){
            $result = DB::table('products')
                    ->where('product_id', $product_id)
                    ->where('kvi', 69010)
                    ->count();
            if ($result > 0)
                $result = true;
        }
        return $result;
    }
    
    /*
     * Class Name: checkFreeProds
     * Description: Code to get the product ids for which free prods not available
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 15 Sept 2016
     * Modified Date & Reason: 

     */

    public function checkFreeProds($cartdata, $wh_id, $segmentId, $token, $customerId,$customer_type='') {
        try {
            $products_free_not_avial = array();
            foreach ($cartdata as $cd) {
                $product_id = $cd['product_id'];
                $parent_id = $cd['parent_id'];
                $quantity = $cd['total_qty'];
                $total_price = $cd['total_price'];
                $warehouseId = DB::select("SELECT GetCPInventoryStatus(" . $product_id . ",'" . $wh_id . "',$segmentId,4) as le_wh_id ");
                $le_wh_id = $warehouseId[0]->le_wh_id;
                if (($le_wh_id == 0) || empty($le_wh_id)) {
                    $products_free_not_avial[] = $parent_id;
                } else {
                    if ($customer_type == 3016) {
                        $query = DB::table('inventory')
                                ->select(DB::raw('(dit_qty-(dit_order_qty+dit_reserved_qty)) as availQty'))
                                ->where('product_id', '=', $product_id)
                                ->where('le_wh_id', '=', $le_wh_id)
                                ->get()->all();
                    } else {
                        $checkInventory = DB::table('inventory')
                                ->select(DB::raw('inv_display_mode'))
                                ->where('product_id', '=', $product_id)
                                ->where('le_wh_id', '=', $le_wh_id)
                                ->get()->all();
                        $displaymode = $checkInventory[0]->inv_display_mode;
                        $query = DB::table('inventory')
                                ->select(DB::raw('(' . $displaymode . '-(order_qty+reserved_qty)) as availQty'))
                                ->where('product_id', '=', $product_id)
                                ->where('le_wh_id', '=', $le_wh_id)
                                ->get()->all();
                    }
                    $avail_quantity = $query[0]->availQty;
                    if (($quantity) > $avail_quantity) {
                        $products_free_not_avial[] = $parent_id;
                    } else if (($total_price == '' || $total_price == 0) && ($product_id == $parent_id)) {
                        $products_free_not_avial[] = $product_id;
                    }
                }
            }
            return $products_free_not_avial;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * Function Name: editCart()
     * Description: editCart function is used to get the product multivariant array where
      $api=8 is for editCart API.
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 18 July 2016
     * Modified Date & Reason:
     */

    public function editCart($product_id, $customer_token, $quantity, $le_wh, $segment_id) {
        try {
            $parent_id = DB::table('vw_cp_products')
                    ->select('parent_id')->where('product_id', '=', $product_id)
                    ->get()->all();

            $parent_id = $parent_id[0]->parent_id;
            $productDatas = array();
            $productDatas['product_id'] = $parent_id;

            $product_name = DB::table('products as prod')
                    ->select(DB::raw("prod.product_title as product_name,prod.esu,pc.description,prod.primary_image,prod.mrp"))
                    ->leftJoin('product_content as pc', 'pc.product_id', '=', 'prod.product_id')
                    ->where('prod.product_id', '=', $parent_id)
                    ->get()->all();
            $productDatas['product_name'] = $product_name[0]->product_name;
            $productDatas['description'] = $product_name[0]->description;
            $productDatas['mrp'] = $product_name[0]->mrp;
            $productDatas['rating'] = $this->Review->Review($product_id);
            $productDatas['reviews'] = $this->Review->getReviews($product_id);
            $productDatas['esu'] = $product_name[0]->esu;
            $productDatas['related_products'] = [];
            $productDatas['image'] = $product_name[0]->primary_image;
            $productDatas['primary_image'] = $product_name[0]->primary_image;
            $productDatas['images'] = $this->getMedia($product_id);

            $inventory = 0;
            // if($product['product_id']==4812){    
            $childProducts = $this->getChildProduct($parent_id);
            $childProducts = json_encode($childProducts);
            $childProducts = json_decode($childProducts, true);
            $variantname1 = array_values(array_unique(array_column($childProducts, 'variant_value1')));

            if (!empty($variantname1[0])) {
                $variantsize1 = sizeof($variantname1);
            } else {
                $variantsize1 = 0;
            }
            $variantname2 = array_values(array_unique(array_column($childProducts, 'variant_value2')));
            if (!empty($variantname2[0])) {
                $variantsize2 = sizeof($variantname2);
            } else {
                $variantsize2 = 0;
            }
            $variantname3 = array_values(array_unique(array_column($childProducts, 'variant_value3')));
            if (!empty($variantname3[0])) {
                $variantsize3 = sizeof($variantname3);
            } else {
                $variantsize3 = 0;
            }
            if ($variantsize1 > 0) {
                for ($i = 0; $i < $variantsize1; $i++) {
                    $k = 0;
                    //$l=0;
                    $x = 0;
                    foreach ($childProducts as $childProduct) {
                        if ($childProduct['product_id'] == $product_id) {
                            $ordered_quantity = $quantity;
                            $inventory = $this->getInventory($childProduct['product_id'], $le_wh, $segment_id);
                            $is_default = 1;
                        } else {
                            $inventory = $this->getInventory($childProduct['product_id'], $le_wh, $segment_id);
                            $is_default = 0;
                            $ordered_quantity = 0;
                        }

                        if ($variantname1[$i] == $childProduct['variant_value1']) {

                            //  $productDatas['variants'][$i]['has_inner_varients'] = 1;
                            $productDatas['variants'][$i]['variant_name'] = $childProduct['variant_value1'];
                            // $productDatas['variants'][$i]['has_inner_varients'] = 1;
                            $productDatas['variants'][$i]['product_id'] = $childProduct['product_id'];
                            $productDatas['variants'][$i]['product_name'] = $childProduct['product_title'];


                            if ($childProduct['product_id'] == $product_id) {
                                $ordered_quantity = $quantity;
                                $inventory = $this->getInventory($childProduct['product_id'], $le_wh, $segment_id);
                                $productDatas['variants'][$i]['quantity'] = $inventory;
                                $productDatas['variants'][$i]['ordered_quantity'] = $ordered_quantity;
                                $productDatas['variants'][$i]['is_default'] = $is_default;

                                $is_default = 1;
                            } else {
                                $inventory = $this->getInventory($childProduct['product_id'], $le_wh, $segment_id);
                                $is_default = 0;
                                $ordered_quantity = 0;
                                $productDatas['variants'][$i]['quantity'] = $inventory;
                                $productDatas['variants'][$i]['ordered_quantity'] = $ordered_quantity;
                                $productDatas['variants'][$i]['is_default'] = $is_default;
                            }
                            $productDatas['variants'][$i]['description'] = $this->getDescription($childProduct['product_id']);
                            $productDatas['variants'][$i]['mrp'] = $childProduct['mrp'];
                            $productDatas['variants'][$i]['image'] = $childProduct['primary_image'];
                            $productDatas['variants'][$i]['images'] = $this->getMedia($childProduct['product_id']);
                            $spec = $this->getProductSpecifications($childProduct['product_id']);
                            $productDatas['variants'][$i]['specifications'] = $spec;
                            $Reviews = $this->Review->getReviews($childProduct['product_id']);
                            $productDatas['variants'][$i]['reviews'] = $Reviews;
                            $productDatas['variants'][$i]['esu'] = $childProduct['esu'];
                            $productDatas['variants'][$i]['rating'] = $this->Review->Review($product_id);
                            //$productDatas['variants'][$i]['has_inner_varients'] = 1;

                            if (empty($childProduct['variant_value2'])) {
                                $productDatas['variants'][$i]['has_inner_varients'] = 0;
                                //  $productDatas['variants'][$i]['packs'] = [];
                                $productDatas['variants'][$i]['packs'] = $this->getPackData($childProduct['product_id'], $customer_token, $le_wh);
                            } else {

                                for ($y = 0; $y < $variantsize2; $y++) {
                                    $productDatas['variants'][$i]['has_inner_varients'] = 1;
                                    if ($variantname2[$y] == $childProduct['variant_value2']) {

                                        $productDatas['variants'][$i]['variants'][$k]['product_id'] = $childProduct['product_id'];
                                        $productDatas['variants'][$i]['variants'][$k]['variant_name'] = $childProduct['variant_value2'];
                                        $productDatas['variants'][$i]['variants'][$k]['product_name'] = $childProduct['product_title'];
                                        // $productDatas['variants'][$i]['variants'][$k]['quantity'] = $inventory;

                                        if ($childProduct['product_id'] == $product_id) {
                                            $ordered_quantity = $quantity;
                                            $is_default = 1;
                                            $inventory = $this->getInventory($childProduct['product_id'], $le_wh, $segment_id);
                                            $productDatas['variants'][$i]['variants'][$k]['quantity'] = $inventory;
                                            $productDatas['variants'][$i]['variants'][$k]['ordered_quantity'] = $ordered_quantity;
                                            $productDatas['variants'][$i]['variants'][$k]['is_default'] = $is_default;
                                        } else {
                                            $inventory = $this->getInventory($childProduct['product_id'], $le_wh, $segment_id);
                                            $is_default = 0;
                                            $ordered_quantity = 0;
                                            $productDatas['variants'][$i]['variants'][$k]['quantity'] = $inventory;
                                            $productDatas['variants'][$i]['variants'][$k]['ordered_quantity'] = $ordered_quantity;
                                            $productDatas['variants'][$i]['variants'][$k]['is_default'] = $is_default;
                                        }

                                        $productDatas['variants'][$i]['variants'][$k]['description'] = $this->getDescription($childProduct['product_id']);
                                        $productDatas['variants'][$i]['variants'][$k]['mrp'] = $childProduct['mrp'];
                                        $productDatas['variants'][$i]['variants'][$k]['image'] = $childProduct['primary_image'];
                                        $productDatas['variants'][$i]['variants'][$k]['images'] = $this->getMedia($childProduct['product_id']);

                                        $productDatas['variants'][$i]['variants'][$k]['specifications'] = $this->getProductSpecifications($childProduct['product_id']);

                                        $productDatas['variants'][$i]['variants'][$k]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
                                        $productDatas['variants'][$i]['variants'][$k]['esu'] = $childProduct['esu'];
                                        $productDatas['variants'][$i]['variants'][$k]['rating'] = $this->Review->Review($product_id);

                                        if (empty($childProduct['variant_value3'])) {
                                            $productDatas['variants'][$i]['variants'][$k]['has_inner_varients'] = 0;
                                            //$productDatas['variants'][$i]['variants'][$k]['packs'] = [];
                                            $productDatas['variants'][$i]['variants'][$k]['packs'] = $this->getPackData($childProduct['product_id'], $customer_token, $le_wh);
                                        } else {

                                            $l = 0;
                                            for ($z = 0; $z < $variantsize3; $z++) {

                                                if ($variantname3[$z] == $childProduct['variant_value3']) {
                                                    $productDatas['variants'][$i]['variants'][$k]['has_inner_varients'] = 1;
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['product_id'] = $childProduct['product_id'];
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['variant_name'] = $childProduct['variant_value3'];
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['product_name'] = $childProduct['product_title'];
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['quantity'] = $inventory;

                                                    if ($childProduct['product_id'] == $product_id) {
                                                        $ordered_quantity = $quantity;
                                                        $is_default = 1;
                                                        $inventory = $this->getInventory($childProduct['product_id'], $le_wh, $segment_id);
                                                        $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['quantity'] = $inventory;
                                                        $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['ordered_quantity'] = $ordered_quantity;
                                                        $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['is_default'] = $is_default;
                                                    } else {
                                                        $inventory = $this->getInventory($childProduct['product_id'], $le_wh, $segment_id);
                                                        $is_default = 0;
                                                        $ordered_quantity = 0;
                                                        $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['quantity'] = $inventory;
                                                        $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['ordered_quantity'] = $ordered_quantity;
                                                        $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['is_default'] = $is_default;
                                                    }

                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['description'] = $this->getDescription($childProduct['product_id']);
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['mrp'] = $childProduct['mrp'];
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['image'] = $childProduct['primary_image'];
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['images'] = $this->getMedia($childProduct['product_id']);

                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['specifications'] = $this->getProductSpecifications($childProduct['product_id']);

                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['esu'] = $childProduct['esu'];
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['rating'] = $this->Review->Review($product_id);
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['has_inner_varients'] = 0;
                                                    $productDatas['variants'][$i]['variants'][$k]['variants'][$l]['packs'] = $this->getPackData($childProduct['product_id'], $customer_token, $le_wh);
                                                    //$productDatas['variants'][$i]['variants'][$k]['variants'][$l]['packs'] = []; 
                                                    $l++;
                                                }
                                            }
                                        }
                                        $k++;
                                    }
                                }
                            }
                            $x++;
                        }//variantname1 condition   
                    }//$i increment
                }//child product foreach loop
            } //variantsize1 not empty condition
            return $productDatas;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * Function Name: getMedia()
     * Description: getMedia function is used to get the multiple product images for the product_id passed.
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 18 July 2016
     * Modified Date & Reason:
     */

    public function getMedia($product_id) {
        $image1 = DB::table('product_media as pm')
                ->select('pm.url as image')
                ->where('pm.product_id', '=', $product_id)
                ->where('pm.media_type', '=', '85003')
                ->get()->all();
        $image2 = DB::table('products as p')
                ->select('p.primary_image as image')
                ->where('p.product_id', '=', $product_id)
                ->get()->all();
        $image = array_merge($image2, $image1);

        return $image;
    }

    /*
     * Function Name: getInventory()
     * Description: getInventory function is used to fetch the inventory based on the pincode, product_id and segment_id
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 18 July 2016
     * Modified Date & Reason:
     */

    public function getInventory($product_id, $le_wh, $segment_id) {
        $inventory = DB::select("SELECT GetCPInventoryStatus($product_id,'" . $le_wh . "',$segment_id,4) as inventory");
        $inventory = $inventory[0]->inventory;
        return $inventory;
    }

    /*
     * Function Name: getChildProduct()
     * Description: getChildProduct function is used to get the child product of the product_id passed
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 18 July 2016
     * Modified Date & Reason:
     */

    public function getChildProduct($product_id) {
        $product_id = '"' . $product_id . '"';
        $query = DB::select("CALL  getCpProducts($product_id)");
        return $query;
    }

    /*
     * Function Name: getProductSpecifications()
     * Description: getProductSpecifications function is used to get the product specifiation for the product_id passed.
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 18 July 2016
     * Modified Date & Reason:
     */

    public function getProductSpecifications($product_id) {
        $result = DB::table('product_attributes as pa')
                ->select('pa.attribute_id', 'pa.value', 'a.name')
                ->leftJoin('attributes as a', 'a.attribute_id', '=', 'pa.attribute_id')
                ->where('pa.product_id', '=', $product_id)
                ->where('pa.value', '!=', '')
                ->get()->all();
        return $result;
    }

    /*
     * Function Name: getDescription()
     * Description: getDescription function is used to get the product Description for the product_id passed.
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 15th Sept 2016
     * Modified Date & Reason:
     */

    public function getDescription($product_id) {
        $desc = DB::table('product_content')
                ->select('description')
                ->where('product_id', '=', $product_id)
                ->get()->all();
        if (!empty($desc)) {
            $desc = $desc[0]->description;
        }
        return $desc;
    }

    /*
     * Function Name: addcart1
     * Description: addcart function is used to check quanity of product and return avail qty & status.      
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 15th Sept 2016
     * Modified Date & Reason:
     */

    public function addcart1($product_id, $quantity, $wh_id, $segmentId, $cust_data) {
        $customer_id = $cust_data['customer_id'];
        $customer_type = $cust_data['customer_type'];
        
        $warehouseId = DB::select("SELECT GetCPInventoryStatus(" . $product_id . ",'" . $wh_id . "',$segmentId,4) as le_wh_id ");
        $le_wh_id = $warehouseId[0]->le_wh_id;
        if (($le_wh_id == 0) || empty($le_wh_id)) {
            $data['status'] = 0;
            $data['product_id'] = $product_id;
            $data['available_quantity'] = 0;
        } else {
            if ($customer_type == 3016) {
                $query = DB::table('inventory')
                        ->select(DB::raw('(dit_qty-(dit_order_qty+dit_reserved_qty)) as availQty'))
                        ->where('product_id', '=', $product_id)
                        ->where('le_wh_id', '=', $le_wh_id)
                        ->get()->all();
            } else {
                $checkInventory = DB::table('inventory')
                        ->select(DB::raw('inv_display_mode'))
                        ->where('product_id', '=', $product_id)
                        ->where('le_wh_id', '=', $le_wh_id)
                        ->get()->all();
                $displaymode = $checkInventory[0]->inv_display_mode;
                $query = DB::table('inventory')
                        ->select(DB::raw('(' . $displaymode . '-(order_qty+reserved_qty)) as availQty'))
                        ->where('product_id', '=', $product_id)
                        ->where('le_wh_id', '=', $le_wh_id)
                        ->get()->all();
            }
            $avail_quantity = $query[0]->availQty;
            if (($quantity) > $avail_quantity) {
                $data['status'] = 0;
                $data['product_id'] = $product_id;
                $data['available_quantity'] = $avail_quantity;
                $this->inventoryRequest($product_id, $wh_id, $segmentId, $quantity, $customer_id);
            } else {
                $data['status'] = 1;
                $data['product_id'] = $product_id;
                $data['available_quantity'] = $avail_quantity;
            }
        }
        return $data;
    }

    /*
     * Class Name: checkfreebie
     * Description: check product is freebie or not
     * Author: Ebutor move to <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 12th Dec2016
     * Modified Date & Reason: 
     */

    public function checkfreebie($prod_id) {
        $freeProdid = DB::table("products")
                ->select("product_title")
                ->where("kvi", "=", 69010)
                ->where("is_sellable", "=", 0)
                ->where("product_id", "=", $prod_id)
                ->get()->all();
        $mainProd = DB::table("products")
                ->select("product_title")
                ->where("product_id", "=", $prod_id)
                ->get()->all();
        if (!empty($freeProdid)) {
            $data['status'] = 'true';
            $data['product_title'] = $mainProd[0]->product_title;
        } else {
            $data['status'] = 'false';
            $data['product_title'] = $mainProd[0]->product_title;
        }
        return $data;
    }

    //getRetailerInfo
    public function getRetailerInfo($custId) {
        try {
            $result = DB::Table('legal_entities as l')
                            ->select(['l.business_legal_name', 'l.address1', 'l.address2', 'l.locality', 'l.city', 'l.pincode', 'zone.name as state_name','u.mobile_no'])
                            ->leftJoin('zone', 'zone.zone_id', '=', 'l.state_id')
                            ->leftJoin('users as u','u.legal_entity_id','=','l.legal_entity_id')
                            ->where('l.legal_entity_id', $custId)->first();
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    //getRetailerInfo
    public function updateBeat($userId, $custId, $beatId, $le_wh_id, $hub_id) {
        try {
             $le_id = DB::table('legalentity_warehouses')
                    ->select('legal_entity_id')
                    ->where('le_wh_id',$le_wh_id)
                    ->get()->all();
            $beat_name=DB::table('pjp_pincode_area')
                    ->where('pjp_pincode_area_id',$beatId)
                    ->get()->all();
            $update_beat = DB::Table('customers')
                    ->where('le_id', $custId)
                    ->update(array('beat_id' => $beatId,'hub_id'=>$hub_id,'spoke_id'=>$beat_name[0]->spoke_id, 'updated_by' => $userId, 'updated_at' => date("Y-m-d H:i:s")));
            $update_flat=DB::table('retailer_flat')
                        ->where("legal_entity_id",$custId)
                        ->update(array('beat_id' => $beatId,'beat'=>$beat_name[0]->pjp_name, 'hub_id'=>$hub_id,'spoke_id'=>$beat_name[0]->spoke_id,'parent_le_id'=>$le_id[0]->legal_entity_id,'updated_by' => $userId, 'updated_at' => date("Y-m-d H:i:s")));
            $update_le = DB::table('legal_entities')
                        ->where('legal_entity_id',$custId)
                        ->update(array('parent_le_id' => $le_id[0]->legal_entity_id));

        } catch (Exception $ex) {
            
        }
    }

    public function getMastValue($desc) {
        try {
            $result = DB::table('master_lookup')
                    ->select(DB::raw("value"))
                    ->where('description', '=', $desc)
                    ->first();

            if (is_object($result)) {
                return $result->value;
            } else {

                return 0;
            }
        } catch (Exception $e) {

            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function inventoryRequest($productId, $leWhId, $segmentId, $totalQty, $customerId) {
        try {
            if ($productId > 0) {
                $data['product_id'] = $productId;
                $data['le_wh_id'] = $leWhId;
                //$data['hub_id'] = $hubId;
                $data['segment_id'] = $segmentId;
                $data['total_qty'] = $totalQty;
                $data['customer_id'] = $customerId;
                DB::table('inventory_request')->insert($data);
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }
}
