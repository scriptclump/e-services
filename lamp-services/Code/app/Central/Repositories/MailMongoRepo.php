<?php

namespace Central\Repositories;

use DB;
use Token;
use Illuminate\Support\Facades\View;
use App\models\Mongo\MongoMailModel;
use App\Central\Repositories\OrderRepo;
use App\Modules\Orders\Models\OrderModel;
use App\models\Dmapi\dmapiOrders;
use App\Modules\Indent\Models\LegalEntity;
use Mail;


use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Orders\Models\ReturnModel;

class MailMongo {

    private $templateName;
    private $mongoMail = NULL;
    private $templateData = NULL;
    private $filePath = NULL;
    private $_ordermodel = NULL;
    private $_dmapiorders = NULL;
    private $MailHeader;

    public function __construct() {
        $this->mongoMail = new MongoMailModel();
        $this->_ordermodel = new OrderModel();
        $this->_dmapiorders = new dmapiOrders();
        $this->_legalEntityModel = new LegalEntity();
        $path = realpath(dirname(__FILE__));
        $ds = DIRECTORY_SEPARATOR;
        $pathArray = explode($ds, $path);
        array_pop($pathArray);
        array_pop($pathArray);
        array_pop($pathArray);
        $pathArray = implode($ds, $pathArray);
        $laravelViewPath = $pathArray . $ds . 'resources' . $ds . 'views' . $ds . 'emails';
        $this->filePath = $laravelViewPath;
        $this->MailHeader = env('APP_SETUP', '');
    }

    private function _setMailMongomodel() {
        $this->mongoMail = new MongoMailModel();
    }

    public function setTemplate($templatename) {
    //$this->templateName = $templatename;
    }

    public function sendmail($templateName, $orderId) {

        $templateView = $this->mongoMail->getMailTemplateByName($templateName);
        $data = "Mail send call failed";
    if (count($templateView) > 0) { //check db for data
        $function = $templateView[0]->functionName;
        $templateView = $templateView[0]->template;
        $templateView = base64_decode($templateView);
        // data stored in mongo is base64 encoded
        $this->templateData = $templateView;
        $this->templateName = $templateName;
        $this->emailTo();
        //calling function
        $data = $this->$function($orderId);
    } else {
        $this->templateData = NULL;
        $this->templateName = NULL;
    }

    return $data;
    //$this->emailTo($data,$to,$subject);
}
public function sendmailtax($templateName, $orderId,$data,$response) {

    $templateView = $this->mongoMail->getMailTemplateByName($templateName);
    if (count($templateView) > 0) { //check db for data
        $function = $templateView[0]->functionName;
        $templateView = $templateView[0]->template;
        $templateView = base64_decode($templateView);
        // data stored in mongo is base64 encoded
        $this->templateData = $templateView;
        $this->templateName = $templateName;
        $this->emailTo();
        //calling function
        $data = $this->$function($orderId,$data,$response);
    } else {
        $this->templateData = NULL;
        $this->templateName = NULL;
    }

    return $data;
    //$this->emailTo($data,$to,$subject);
}
public function emailTo() {

    //echo "creating new directory";
    if (!is_null($this->templateData)) {

        $ds = DIRECTORY_SEPARATOR;
        $temppath = $this->filePath . $ds . $this->templateName . '.blade.php';
        $file = fopen($temppath, "w");
        fwrite($file, $this->templateData);
        fclose($file);
    } else {
        return false;
    }
}

public function insertMailTemplate($templateName, $template, $status) {
    $this->mongoMail->insertMailTemplate($templateName, $template, $status);
}

/**
 * [placeOrder description]
 * @param  [interger] $orderId [description]
 * @return [null]          [will work according to the mail calss we need]
 */
private function placeOrder($orderId) {

    try {
        $orderInfo = $this->_ordermodel->getOrderDetailById($orderId);
        
        $billingAndShippingArr = $this->_ordermodel->getBillingAndShippingAddressByOrderId($orderId);
        $address = $this->_ordermodel->convertBillingAndShippingAddress($billingAndShippingArr);
        $products = $this->_ordermodel->getCompleteProductByOrderId($orderId);
        $warehouse = $this->_legalEntityModel->getWarehouseById($orderInfo->le_wh_id);
        //$products = jjson_encode($products), true);
        $taxArr = $this->_ordermodel->getProductTaxByOrderId($orderId);
        $taxSummaryArr = $this->_ordermodel->getTaxSummary($taxArr);
        $taxSummary = isset($taxSummaryArr['summary']) ? $taxSummaryArr['summary'] : '';
        $productTaxArr = isset($taxSummaryArr['item']) ? $taxSummaryArr['item'] : '';
        $taxBreakup = isset($taxSummaryArr['breakup']) ? $taxSummaryArr['breakup'] : '';
        $data['taxSummary'] = $taxBreakup;
        $sno = 1;
        $qty_total = 0;
        $tax_amt = 0;
        $tol = 0;
        $final_product_array = array();
        foreach ($products as $details) {
            $price = $this->_ordermodel->getUnitPricesTaxAndWithoutTax($orderId,$details->product_id);
            $product_array['sno'] = $sno;
            $product_array['product_name'] = $details->product_title;
            $product_array['sku'] = isset($details->sku) ? $details->sku : '';
            $product_array['quantity'] = $details->qty;
            $product_array['discount_price'] = $details->discount;
            $product_array['mrp'] = isset($details->mrp) ? $details->mrp : '';
            $product_array['price'] = $details->price;
            $product_array['unit_price'] = $price['singleUnitPrice'];
            $product_array['subtotal'] = $details->total - $details->tax;
            $product_array['tax'] = $details->tax;
            $product_array['tax_percent'] = isset($productTaxArr[$details->product_id]) ? $productTaxArr[$details->product_id].'%' : '0.0%';
            $product_array['total'] = $details->total;
            $qty_total += $details->qty;
            $tax_amt += $details->tax;
            $tol += $details->total;
            $final_product_array[] = $product_array;
            $sno++;
        }

        $data['final_product_array'] = $final_product_array;
        $data['qty_total'] = $qty_total;
        $data['tax_net_amt_tot'] = $tax_amt;
        $data['order_details'] = $orderInfo;
        $data['shipping'] = $address['shipping'];
        $data['billing'] = $address['billing'];
        $data['tol'] = $tol;
        $data['warehouse'] = $warehouse;
        // $data[] = $tax;
        //$response = $orderInfo->getOrderCompleteDetails($orderId);
        $subject = 'New Order'.$this->MailHeader;
        $subject = $subject . ' Order Id ' . $orderInfo->order_code;
        //$imageUrl = url() . '/img/ebutor-logo.png';
        //$response = $data;
        $data['imageUrl'] = 'http://portal1.ebutor.com/assets/admin/layout/img/small-logo.png';
        @\Mail::send(['html' => 'emails.' . $this->templateName], $data, function($message) use ($response, $subject) {
            $message->to(Config('app.EMAIL_NOTIFICATION'))->subject($subject);
        });
        return "Mail Send SucessFully";
    } catch (Exception $e) {
        return "Mail Send Failed";
    }
}

private function updateOrder($orderId) {

    try {
//              
        $data = array();
        $orderInfo = $this->_ordermodel->getOrderDetailById($orderId);
        $billingAndShippingArr = $this->_ordermodel->getBillingAndShippingAddressByOrderId($orderId);
        $address = $this->_ordermodel->convertBillingAndShippingAddress($billingAndShippingArr);
        $products = $this->_ordermodel->getCompleteProductByOrderId($orderId);
        $orderstatus = $this->_dmapiorders->checkOrderStatus($orderInfo->order_status_id);
        //$products = jjson_encode($products), true);
        $taxInfo = $this->_ordermodel->getProductTaxByOrderId($orderId);
        $taxArr = $this->_ordermodel->getTaxSummary($taxInfo);
        $taxsummary_array = array();
        if (isset($taxArr['summary'])) {

            $taxsummary_array = $taxArr['summary'];
        }

        $n = 0;
        $taxsummary = array();
        foreach ($taxsummary_array as $key => $value) {

            $value = json_decode(json_encode($value));
            $taxsummary[$n] = $value;
            $n++;
        }

        $tax_cal = array();
        foreach ($taxArr['item'] as $key => $val) {

            $product_id = $key;
            $tax_cal[$product_id] = $val;
        }
        $data['taxsummary'] = $taxsummary;
        $sno = 1;
        $qty_total = 0;
        $tax_amt = 0;
        $tol = 0;
        foreach ($products as $details) {
            $product_array['sno'] = $sno;
            $product_array['product_name'] = $details->product_title;
            $product_array['sku'] = isset($details->sku) ? $details->sku : '';
            $product_array['quantity'] = $details->qty;
            $product_array['discount_price'] = $details->discount;
            $product_array['mrp'] = isset($details->mrp) ? $details->mrp : '';
            $product_array['price'] = $details->price;
            $product_array['subtotal'] = $details->total - $details->tax;
            $product_array['tax'] = $details->tax;
            $product_array['tax_percent'] = $tax_cal[$details->product_id];
            $product_array['total'] = $details->total;
            $qty_total += $details->qty;
            $tax_amt += $details->tax;
            $tol += $details->total;
            $final_product_array[] = $product_array;
            $sno++;
        }

        $data['final_product_array'] = $final_product_array;
        $data['qty_total'] = $qty_total;
        $data['tax_net_amt_tot'] = $tax_amt;
        $data['order_details'] = $orderInfo;
        $data['shipping'] = $address['shipping'];
        $data['billing'] = $address['billing'];
        $data['tol'] = $tol;
        $data['orderstatus'] = $orderstatus[0];
        $message="Your  Order   "   .$orderId."  has been  updated to  ".$data['orderstatus'];
        $this->_dmapiorders->sendSMS($data['shipping']->telephone, $message);
        // $data[] = $tax;
        //$response = $orderInfo->getOrderCompleteDetails($orderId);
        $subject = 'Your Order '.$this->MailHeader;
        $subject = $subject  . $orderInfo->order_code. '  Update';
        //$imageUrl = url() . '/img/ebutor-logo.png';
        //$response = $data;
        $response['imageUrl'] = '';
        @\Mail::send(['html' => 'emails.' . $this->templateName], $data, function($message) use ($response, $subject) {
            $message->to(Config('app.EMAIL_NOTIFICATION'))->subject($subject);
        });
        return "Mail Sent SucessFully";
    } catch (Exception $e) {
        var_dump($e);
        return "Mail Send Failed";
    }
}
private function cancelOrder($orderId) {

    try {      
        $data = array();
        $orderInfo = $this->_ordermodel->getOrderDetailById($orderId);
        $billingAndShippingArr = $this->_ordermodel->getBillingAndShippingAddressByOrderId($orderId);
        $address = $this->_ordermodel->convertBillingAndShippingAddress($billingAndShippingArr);
        $cancel_grid = $this->_ordermodel->getCancelGridId($orderId);//products need to be changed from ordered to cancelled products            $orderstatus = $this->_dmapiorders->checkOrderStatus($orderInfo->order_status_id);
        //$products = jjson_encode($products), true);
        $taxInfo = $this->_ordermodel->getProductTaxByOrderId($orderId);
        $taxArr = $this->_ordermodel->getTaxSummary($taxInfo);
        $taxsummary_array = array();
        $productArr= array();
        if($cancel_grid){
            foreach ($cancel_grid as $cancel){
                $productArr = $this->_ordermodel->getCancelledProductById($cancel->cancel_grid_id);
                $productArr[] = $products;
            }
        }
        if (isset($taxArr['summary'])) {

            $taxsummary_array = $taxArr['summary'];
        }

        $n = 0;
        $taxsummary = array();
        foreach ($taxsummary_array as $key => $value) {

            $value = json_decode(json_encode($value));
            $taxsummary[$n] = $value;
            $n++;
        }

        $tax_cal = array();
        foreach ($taxArr['item'] as $key => $val) {

            $product_id = $key;
            $tax_cal[$product_id] = $val;
        }
        $data['taxsummary'] = $taxsummary;
        $sno = 1;
        $qty_total = 0;
        $tax_amt = 0;
        $tol = 0;
        foreach ($products as $details) {
            $product_array['sno'] = $sno;
            $product_array['product_name'] = $details->product_title;
            $product_array['sku'] = isset($details->sku) ? $details->sku : '';
            $product_array['quantity'] = $details->qty;
            $product_array['discount_price'] = $details->discount;
            $product_array['mrp'] = isset($details->mrp) ? $details->mrp : '';
            $product_array['price'] = $details->price;
            $product_array['subtotal'] = $details->total - $details->tax;
            $product_array['tax'] = $details->tax;
            $product_array['tax_percent'] = $tax_cal[$details->product_id];
            $product_array['total'] = $details->total;
            $qty_total += $details->qty;
            $tax_amt += $details->tax;
            $tol += $details->total;
            $final_product_array[] = $product_array;
            $sno++;
        }

        $data['final_product_array'] = $final_product_array;
        $data['qty_total'] = $qty_total;
        $data['tax_net_amt_tot'] = $tax_amt;
        $data['order_details'] = $orderInfo;
        $data['shipping'] = $address['shipping'];
        $data['billing'] = $address['billing'];
        $data['tol'] = $tol;
        $data['orderstatus'] = $orderstatus[0];

        $message="Your  Order   "   .$orderId."  has been ".$data['orderstatus'];
        $this->_dmapiorders->sendSMS($data['shipping']->telephone, $message);
        // $data[] = $tax;
        //$response = $orderInfo->getOrderCompleteDetails($orderId);
        $subject = 'Your Order '.$this->MailHeader;
        $subject = $subject   .  $orderInfo->order_code.'   is Cancelled';
        @\Mail::send(['html' => 'emails.' . $this->templateName], $data, function($message) use ($response, $subject) {
            $message->to(Config('app.EMAIL_NOTIFICATION'))->subject($subject);
        });
        return "Mail Sent SucessFully";
    } catch (Exception $e) {
        var_dump($e);
        return "Mail Send Failed";
    }
}
private function taxInfo($orderId,$taxdata,$response) {

    try {          
        $data = array();
        $data['text']=$taxdata;
        $data['response']=$response;
        $subject = $this->MailHeader."TaxInformation failure on order id $orderId";
        $response = $data;
        @\Mail::send(['html' => 'emails.' . $this->templateName], $data, function($message) use ($response, $subject) {
            $message->to(Config('app.EMAIL_NOTIFICATION'))->subject($subject);
        });
        return "Mail Sent SucessFully";
    } catch (Exception $e) {
        var_dump($e);
        return "Mail Send Failed";
    }
}


    /**
     * [placeOrder description]
     * @param  [interger] $orderId [description]
     * @return [null]          [will work according to the mail calss we need]
     */
    private function returnOrder($returnId) {

        $this->_masterLookup = new MasterLookup();
        $this->_returnModel = new ReturnModel ();

        $reasons_status = $this->_masterLookup->getMasterLookupByCategoryName('Return Reasons');
        $returnArr = $this->_returnModel->getReturnDetailById($returnId);
        $status_id = isset($returnArr[0]->return_status_id) ? $returnArr[0]->return_status_id : '17007';
        if (!isset($returnArr) || count($returnArr) == 0) {
           return "Mail Send Failed";
           exit;
        }
        $orderId = $returnArr[0]->gds_order_id;
        $return_status = $this->_masterLookup->getAllOrderStatus('RETURNS');
        $allProductsArr = $this->_ordermodel->getProductByOrderId($orderId);
        $commentStatusArr = $this->_ordermodel->getOrderStatus('RETURNS');
        $commentArr = $this->_ordermodel->getOrderCommentById($orderId, 'RETURNS');
        $productArr = array();
        foreach ($allProductsArr as $product) {
            $productArr[$product->product_id] = $product;
        }
        $billingAndShippingArr = $this->_ordermodel->getBillingAndShippingAddressByOrderId($orderId);
        $billingAndShipping = $this->_ordermodel->convertBillingAndShippingAddress($billingAndShippingArr);
        $orders = $this->_ordermodel->getOrderDetailById($orderId);
        
        $data['returnProductArr'] = $returnArr;
        $data['commentStatusArr'] = $commentStatusArr;
        $data['statusMatrixArr'] = $return_status;
        $data['commentArr'] = $commentArr;
        $data['productArr'] = $productArr;
        $data['orderdata'] = $orders;
        $data['billing'] = (isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] : '');
        $data['shipping'] = (isset($billingAndShipping['shipping']) ? $billingAndShipping['shipping'] : '');
        $data['returnReason'] = $reasons_status;
        $data['imageUrl'] = 'http://portal1.ebutor.com/assets/admin/layout/img/small-logo.png';
        $subject = 'Return Order '. $returnArr[0]->reference_no;
        $response['imageUrl'] = '';
        // $mailAdditions = $this->getUserEmailByRoleName(array('Logistics Manager'));
        // $mailAdditions = json_decode(json_encode($mailAdditions),true);
        //var_dump($mailAdditions); exit;
        \Mail::send(['html' => 'emails.' . $this->templateName], $data, function($message) use ($response, $subject) {
                    $message->to(Config('app.EMAIL_NOTIFICATION'))->subject($subject);
                });
        return "Mail Send SucessFully";
    }
    
    /***
        Remove it later 
    ***/
    public function getUserEmailByRoleName($roleName) {
       try {
           $query = DB::table('users')->select('users.email_id');
           $query->join('user_roles', 'users.user_id', '=', 'user_roles.user_id');
           $query->join('roles', 'roles.role_id', '=', 'user_roles.role_id');
           $query->where('users.is_active', 1);
           return $query->whereIn('roles.name', $roleName)->get()->all();
       } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
       }
   }
}

?>
