<?php
  /*
    * Filename: DotmatrixController.php
    * Description: This file is used for printing in DotMatrix
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor@2017
    * Version: v1.0
    * Created date: 10th April 2017
    * Modified date: 
  */
  
  /*
    * DotmatrixController is used to manage orders
    * @author    Ebutor <info@ebutor.com>
    * @copyright ebutor@2017
    * @package   Orders
    * @version:  v1.0
  */ 
  namespace App\Modules\Cpmanager\Controllers;
  use Illuminate\Support\Facades\Input;
  use Session;
  use Response;
  use Log;
  use URL;
  use DB;
  use Lang;
  use Config;
  use View;
  use Illuminate\Http\Request;
  use App\Modules\Cpmanager\Views\order;
  use App\Http\Controllers\BaseController;
  use App\Modules\DmapiV2\Models\Dmapiv2Model;
  use App\Modules\Orders\Models\OrderModel;
  use App\Modules\Orders\Models\Invoice;
  use App\Modules\Orders\Models\OrderTrack;
  use App\Modules\Indent\Models\LegalEntity;
  use App\Modules\Orders\Models\MasterLookup;
  use App\Modules\Cpmanager\Models\DotmatrixModel;
  use Utility;
  use App\Modules\Orders\Controllers\InvoiceController;
  use app\Modules\Grn\Controllers\GrnController;
  use  App\Modules\Orders\Controllers\OrdersController;

  class DotmatrixController extends BaseController {  
    
    protected $order;
    protected $_Dmapiv2Model;
    protected $_orderModel;
    protected $_OrderTrack;
    protected $_leModel;
    protected $_masterLookup;
    protected $_dotmatrixModel;

    public function __construct() {
      $this->order = new OrderModel();
      $this->_Dmapiv2Model = new Dmapiv2Model();
      $this->_orderModel = new OrderModel();
      $this->_invoiceModel = new Invoice();
      $this->_OrderTrack = new OrderTrack();
      $this->_leModel = new LegalEntity();
      $this->_masterLookup = new MasterLookup();
      $this->_dotmatrixModel = new DotmatrixModel();
    }


    public function printInvoiceDotMatrix() {

        try
        {

           $data = Input::all();
           $resultArray = array();
           $finalResultArray = array();

           $postData = json_decode($data['data'], true);
           $orderIds = explode(',',$postData['orderIds']);

            
            foreach($orderIds as $orderId) {
            $products = $this->_invoiceModel->getInvoiceProductByOrderId($orderId);
            $ecash_applied = isset($products[0]->ecash_applied)?$products[0]->ecash_applied:'';

            if(isset($products[0]->gds_order_id)){
                $orderId = $products[0]->gds_order_id;
                $orderDetails = $this->_orderModel->getOrderDetailById($orderId);

                $trackInfo = $this->_OrderTrack->getTrackDetailByOrderId($orderId);
                $taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);
                $billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
                $billingAndShipping = $this->convertBillingAndShippingAddress($billingAndShippingArr);
                $legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id,$orderDetails->le_wh_id);

                $taxSummaryArr = $this->_orderModel->getTaxSummary($taxArr);
                $taxSummary = isset($taxSummaryArr['summary']) ? $taxSummaryArr['summary'] : '';
                $productTaxArr = isset($taxSummaryArr['item']) ? $taxSummaryArr['item'] : '';
                $taxBreakup = isset($taxSummaryArr['breakup']) ? $taxSummaryArr['breakup'] : '';
                //echo '<pre>';print_r($orderDetails);die;
                $leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
                $lewhInfo = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);
                
                $prodTaxes = array();
                
                foreach ($taxArr as $tax) {
                    $prodTaxes[$tax->product_id] = array('name'=>$tax->name, 'tax_value'=>$tax->tax_value, 'tax'=>$tax->tax, 
                            'cgstPer'=>(($tax->tax * $tax->CGST)/100),  
                            'sgstPer'=>(($tax->tax * $tax->SGST)/100), 
                            'igstPer'=>(($tax->tax * $tax->IGST)/100), 
                            'utgstPer'=>(($tax->tax * $tax->UTGST)/100));
                }
                $companyInfo = $this->_leModel->getCompanyAccountByLeId($orderDetails->legal_entity_id);
                $userInfo = '';
                if($orderDetails->created_by) {
                    $userInfo = $this->_leModel->getUserById($orderDetails->created_by);
                }

                $delSlots = $this->_masterLookup->getMasterLookupByCategoryName('Delivery Slots');                    
                $cratesList = $this->_orderModel->getContainerInfoByOrderId($orderId);
                $pickerInfo = $this->_OrderTrack->getPickerByOrderId($orderId);            
            }

                $printString= '';
            if(isset($leInfo) && isset($lewhInfo)) {
                $printString.= $leInfo->business_legal_name."\n";
                $printString.= $lewhInfo->address1.", ";
                $printString.= (empty($lewhInfo->address2) ? '' : $lewhInfo->address2.", ");
                $printString.= $lewhInfo->city.", ";
                $printString.= $lewhInfo->state_name.", ";
                $printString.= (empty($lewhInfo->country_name) ? 'India, ' : $lewhInfo->country_name.", ");
                $printString.= $lewhInfo->pincode.", ".$lewhInfo->state_code.",\n";
                // $printString.= "<b>State Code:</b>".$lewhInfo->state_code."\n";
                $printString.= " GSTIN No: ".$lewhInfo->tin_number;
                $printString.=" FSSAI No: ".$lewhInfo->fssai;
            }

            $resultArray['Address1'] = $printString;

            /**
             * Shipping address of retailer
             */
                                    
            $shipping = $billingAndShipping['shipping'];

            $shippingArr = ucwords($orderDetails->shop_name)."\n";
            $shippingArr = $shippingArr."".$shipping->fname." ".$shipping->mname." ".$shipping->lname."\n";
            $shippingArr = $shippingArr."".$shipping->addr1." ".$shipping->addr2."\n";

            if(!empty($shipping->locality)) {
                $shippingArr = $shippingArr."".$shipping->locality.", ";
            }
            
            if(!empty($shipping->landmark)) {
                $shippingArr = $shippingArr."".$shipping->landmark.", ";
            }
            
            if(!empty($shipping->city)){
                $shippingArr = $shippingArr."".$shipping->city.", ";
            }

            $shippingArr = $shippingArr."".$shipping->state_name.", ".$shipping->country_name.", ".$shipping->postcode.", ".$shipping->state_code;

            $shippingArr = $shippingArr.",Telephone: ".$shipping->telephone.",\n";
            
            if(!empty($shipping->mobile)) {
                $shippingArr = $shippingArr."Mobile: ".$shipping->mobile.", ".",\n";
            }
            
            if(!empty($orderDetails->beat) && ( $orderDetails->legal_entity_type_id!=1014 && $orderDetails->legal_entity_type_id!=1016)) {
                $shippingArr = $shippingArr."Beat: ".$orderDetails->beat."\n";
            }
           
            // if(!empty($orderDetails->areaname)) {
            //     $shippingArr = $shippingArr."Area: ".$orderDetails->areaname."\n";
            // }
            
            if(!empty($shipping->gstin)){
            if(!is_null($shipping->gstin)) {
                $shippingArr = $shippingArr."GSTIN / UIN: ".$shipping->gstin."\n";   
            } else {
                $shippingArr = $shippingArr."GSTIN / UIN: N/A \n";   
            }
            }
            if(isset($shipping->fssai) && !empty($shipping->fssai)){
              $shippingArr = $shippingArr."FSSAI No: ".$shipping->fssai."\n";
            }else {
              $shippingArr = $shippingArr."FSSAI No: N/A \n";   
            }
            $outputshippingArr = wordwrap($shippingArr,29,"\n");

            $resultArray['Address2'] = $shippingArr;
            /**
             * Invoice details
             */
            
            $invoiceArr = " Invoice No: ";
            if(isset($products[0]->invoice_code)) {
                $invoiceArr = $invoiceArr."".$products[0]->invoice_code.",\n";
            }
            else {
                $invoiceArr = $invoiceArr."".$products[0]->gds_invoice_grid_id.",\n";
            }
            
            $invoiceArr = $invoiceArr." Invoice Date: ".date('d-m-Y', strtotime($products[0]->invoice_date)).",\n";
            $invoiceArr = $invoiceArr." SO No: ".$orderDetails->order_code.",\n";
            $invoiceArr = $invoiceArr." Date: ".date('d-m-Y', strtotime($orderDetails->order_date)).",\n";

            // if(!empty($lewhInfo->le_wh_code)){
            //     $invoiceArr = $invoiceArr."DC No: ".$lewhInfo->le_wh_code."\n";
            // }
            
            // if(!empty($lewhInfo->le_wh_name)){
            //     $invoiceArr = $invoiceArr."DC Name: ".$lewhInfo->le_wh_name."\n";
            // }
            
            // if(!empty($orderDetails->hub_name)){
            //     $invoiceArr = $invoiceArr."Hub Name: ".$orderDetails->hub_name."\n";
            // }
            
            if(isset($userInfo->firstname) && isset($userInfo->lastname) && ($orderDetails->legal_entity_type_id !=1014 && $orderDetails->legal_entity_type_id!=1016)){
                $invoiceArr = $invoiceArr."SO Name: ".$userInfo->firstname." ".$userInfo->lastname."\n";
            }
            
            if(isset($userInfo->mobile_no)){
                $invoiceArr = $invoiceArr."(M: ".$userInfo->mobile_no.")\n";
            }
            
            // if(isset($pickerInfo->firstname) && isset($pickerInfo->lastname)){
            //     $invoiceArr = $invoiceArr."Picker Name: ".$pickerInfo->firstname." ".$pickerInfo->lastname."\n";
            // }
           
            // if(isset($delSlots[$orderDetails->pref_slab1]) && $delSlots[$orderDetails->pref_slab1]!=''){
            //     $invoiceArr = $invoiceArr."Del Slot1: ".$delSlots[$orderDetails->pref_slab1]."\n";
            // }
            
            // if(isset($delSlots[$orderDetails->pref_slab2]) && $delSlots[$orderDetails->pref_slab2]!=''){
            //     $invoiceArr = $invoiceArr."Del Slot2: ".$delSlots[$orderDetails->pref_slab2]."\n";
            // }

            // $invoiceArr = $invoiceArr."Sch Delivery Date: ".date('d-m-Y',strtotime($orderDetails->scheduled_delivery_date))."\n";

            $resultArray['Address3'] = $invoiceArr;

            $productArray = array();

            /**
             * Initializations for the variables of product details
             */
            
            $sno              = 0;
            $x                = 0;
            $sub_total        = 0;
            $total_qty        = 0;
            $InvoicedQty      = 0;
            $total_inv_cfc    = 0;
            $total_unit_price = 0;
            $total_mrp        = 0;
            $total_net        = 0;
            $total_discount   = 0;
            $total_tax        = 0;
            $total_tax_value  = 0;

            $tax            = 0;
            $discount       = 0;
            $shippingAmount = 0;
            $otherDiscount  = 0;
            $grandTotal     = 0;
            $totInvoicedQty = 0;
            $finalTaxArr    = array();

            $totCGST = $totSGST = $totIGST = $totUTGST = 0;
            $printSkuString = '';
            foreach($products as $product)
            {
                $sno = ++$x;
                $taxName   = (isset($prodTaxes[$product->product_id]['name']) ? $prodTaxes[$product->product_id]['name'] : 0);
                $taxPer    = (isset($prodTaxes[$product->product_id]['tax']) ? $prodTaxes[$product->product_id]['tax'] : 0);
                $tax_value = (isset($prodTaxes[$product->product_id]['tax_value']) ? $prodTaxes[$product->product_id]['tax_value'] : 0);

                $singleUnitPrice = (($product->total / (100 + $taxPer) * 100) / $product->qty);
                
                $unitPrice = ($singleUnitPrice * $product->invoicedQty);
                $taxValue  = (($product->single_unit_price * $taxPer) / 100) * $product->invoicedQty;
                $netValue  = ($product->single_unit_price * $product->invoicedQty);
                $subTotal  = $taxValue + $netValue;
                $discount  = 0;
                $taxkey    = $taxName . '-' . $taxPer;
                if ($taxkey != '0-0') {
                    $finalTaxArr[$taxkey][] = array(
                        'tax' => $taxPer,
                        'name' => $taxName,
                        'qty' => $product->qty,
                        'tax_value' => $tax_value,
                        'taxamtPer' => ($tax_value / $product->qty),
                        'taxamt' => (($tax_value / $product->qty) * $product->invoicedQty)
                    );
                }


                $totCGST = $totCGST + $product->CGST;
                $totSGST = $totSGST + $product->SGST;
                $totIGST = $totIGST + $product->IGST;
                $totUTGST = $totUTGST + $product->UTGST;


                $cgstPer = isset($prodTaxes[$product->product_id]['cgstPer']) ? $prodTaxes[$product->product_id]['cgstPer'] : 0;
                $sgstPer = isset($prodTaxes[$product->product_id]['sgstPer']) ? $prodTaxes[$product->product_id]['sgstPer'] : 0;
                $igstPer = isset($prodTaxes[$product->product_id]['igstPer']) ? $prodTaxes[$product->product_id]['igstPer'] : 0;
                $utgstPer = isset($prodTaxes[$product->product_id]['utgstPer']) ? $prodTaxes[$product->product_id]['utgstPer'] : 0;
                if($sgstPer != 0){
                   $sgstPer = isset($prodTaxes[$product->product_id]['sgstPer']) ? $prodTaxes[$product->product_id]['sgstPer'] : 0;
                   $SGST = $product->SGST;
                }elseif($utgstPer!= 0){
                  $sgstPer = isset($prodTaxes[$product->product_id]['utgstPer']) ? $prodTaxes[$product->product_id]['utgstPer'] : 0;
                  $SGST =  $product->UTGST;
                }else{
                  $sgstPer =0.0;
                  $SGST =0.0;
                }
                

                $taxArr = number_format($product->item_tax_amount, 2);
                
                
                $sub_total      = $sub_total + $subTotal;
                $total_discount = $total_discount + $discount;
                $total_net      = $total_net + $netValue;
                $total_qty      = $total_qty + $product->qty;
                $InvoicedQty    = $InvoicedQty + $product->invoicedQty;
                $total_inv_cfc  = $total_inv_cfc + $product->invCfc;
                $total_tax      = $total_tax + $taxValue;
                $cfcletter = isset($product->cfcName[0])?$product->cfcName[0]:'';
                 $productArray[] = array('name'=>$product->pname,
                                          'hsn_code'=>$product->hsn_code,
                                          'mrp'=> number_format((float)$product->mrp,2),
                                          'up'=>number_format($product->single_unit_price, 2),
                                          'oq'=>(int)$product->qty,
                                          'iq'=>(int)$product->invoicedQty,
                                          'invcfc'=>number_format($product->invCfc, 2).' '.$cfcletter,
                                          'cfcname'=>isset($product->cfcName)?$product->cfcName:'',
                                          'net'=>number_format($netValue, 2),
                                          'taxper'=>number_format($taxPer, 2),
                                          'tax'=>$taxArr,
                                          'sd'=>number_format($discount, 2),
                                          'subtot'=>number_format($subTotal, 2),
                                          'CGST'=>number_format($product->CGST, 2),
                                          'CGST_PER'=>number_format($cgstPer, 1),


                                          'SGST'=>number_format($SGST, 2),
                                          'SGST_PER'=>number_format($sgstPer, 1),


                                          'IGST'=>number_format($product->IGST, 2),
                                          'IGST_PER'=>number_format($igstPer, 1),
                                          
                                          ////'UTGST'=>number_format($product->UTGST, 2),
                                          //'UTGST_PER'=>number_format($utgstPer, 1),
                                          );


            }
            
            $resultArray['Products'] = $productArray;

            $totalArray = array('oq'=>$total_qty,
                                'iq'=>$InvoicedQty,
                                'invcfc'=>number_format($total_inv_cfc, 2),
                                'net'=>number_format($total_net, 2),
                                'tax'=>number_format($total_tax, 2),
                                'sd'=>number_format($total_discount, 2),
                                'subtot'=>number_format($sub_total, 2),
                                'SGST'=>number_format($totSGST,2),
                                'CGST'=>number_format($totCGST, 2),
                                'IGST'=>number_format($totIGST, 2),
                                'UTGST'=>number_format($totUTGST, 2)              
                                );    
            $resultArray['totalArray'] = $totalArray;


            if($totSGST > 0 || $totCGST > 0 || $totIGST > 0) {
              $gstData = array('SGST'=>number_format($totSGST,2), 'CGST'=>number_format($totCGST, 2), 'IGST'=>number_format($totIGST, 2), 'UTGST'=>number_format($totUTGST, 2));
            }
            else {
              $gstData = array('SGST'=>'','CGST'=>'','IGST'=>'','UTGST'=>'');
            }


            // The Entire Code Below is about VAT Tax
            $finalNewTaxArr = array();
            foreach ($finalTaxArr as $key => $taxArr) {
                $finalNewTaxArr[$key] = array();
                $totAmt               = 0;
                foreach ($taxArr as $tax) {
                    $totAmt                       = $totAmt + $tax['taxamt'];
                    $finalNewTaxArr[$key]['name'] = $tax['name'];
                    $finalNewTaxArr[$key]['tax']  = $tax['tax'];
                }
                $finalNewTaxArr[$key]['tax_value'] = $totAmt;
            }

            if(isset($finalNewTaxArr) && is_array($finalNewTaxArr))
            {
                foreach($finalNewTaxArr as $tax)
                    $vat = $tax['name']." ".(isset($tax['tax']) ? (float)$tax['tax'] : 0).'%';
            }

            $totInvQty = $InvoicedQty;
            $subTotal =  number_format($sub_total, 2);
            $shippingAmt =  0;
            $totSchDisc =  number_format($total_discount, 2);
            $otherDisc =  number_format($orderDetails->discount, 2);
            $totDisc =  number_format(($total_discount + $orderDetails->discount), 2);
            $billDisc =  isset($products[0]->bill_disc_amt) ? number_format($products[0]->bill_disc_amt,2) :0;


            
            $vat_5 = "-----";
            $vat_14 = "--------";
            if(isset($finalNewTaxArr) && is_array($finalNewTaxArr))                                     
            {
                foreach($finalNewTaxArr as $tax)
                {
                    if((int)$tax['tax'] == 5)
                        $vat_5= number_format((isset($tax['tax_value']) ? ($tax['tax_value']) : 0), 2);
                    if((int)$tax['tax'] == 14)
                        $vat_14= number_format((isset($tax['tax_value']) ? ($tax['tax_value']) : 0), 2);
                }
                
            }
            $totalTax = number_format($total_tax, 2);
            
            $grandTotal          = $sub_total-$billDisc-$ecash_applied;
            $grandTotalWithRound = Utility::getRoundOff($grandTotal, 'gtround');
            $roundoff            = Utility::getRoundOff($grandTotal, 'roundoff');
            
            $roundOff = number_format($roundoff, 2);
            $grandTotal = number_format($grandTotalWithRound, 2);

            //// TIQ = 5, ST = 8, SA = 10, TSD = 9, OD = 6, TD = 5, V5= 5, v14 = 8, TT = 4, ROff = 6, GT = 12

            $resultArray['totalArray'] = $totalArray;

            $vatArray = array('invqty'=>$totInvQty,
                              'subtot'=>$subTotal,
                              'ship'=>$shippingAmt,
                              'shdisc'=>$totSchDisc,
                              'othdisc'=>$otherDisc,
                              'totdisc'=>$totDisc,
                              'billdisc'=>$billDisc,
                              'vat5'=>$vat_5,
                              'vat14'=>$vat_14,
                              'tottax'=>$totalTax,
                              'ecash_applied'=>$ecash_applied,
                              'roundOff'=>$roundOff,
                              'grandTot'=>$grandTotal  
                              );
            $vatArray = array_merge($vatArray,$gstData);
            $resultArray['vatArray'] = $vatArray;



            $trackArray = array();

            $resultArray['trackArray'] = array('cfc'=>0,
                                                'cfc_list'=>'',
                                                'crates'=>0,
                                                'crates_list'=>'',
                                                'bags'=>0,
                                                'bags_list'=>'');

            $cratesList = $this->_orderModel->getContainerInfoByOrderId($orderId);

            if(isset($cratesList['16004'])) {
                $resultArray['trackArray']['cfc_list'] = $cratesList['16004']; 
            }
            if(isset($cratesList['16006'])) {
                $resultArray['trackArray']['bags_list'] = $cratesList['16006']; 
            }
            if(isset($cratesList['16007'])) {
                $resultArray['trackArray']['crates_list'] = $cratesList['16007']; 
            }


            if(isset($trackInfo))
            {                                
                if($trackInfo->cfc_cnt!=0)                    
                {                    
                    $resultArray['trackArray']['cfc'] = (int)$trackInfo->cfc_cnt;
                }               
                // Bags                
                if($trackInfo->bags_cnt!=0)
                {                    

                    $resultArray['trackArray']['bags'] = (int)$trackInfo->bags_cnt;

  
                }                
                //Crates                
                if($trackInfo->crates_cnt!=0)                
                {                    
                    
                    $resultArray['trackArray']['crates'] = (int)$trackInfo->crates_cnt;

                }            
            }

            $grandTotal = str_replace( ",", "", $grandTotal);

            $resultArray['inwords'] = Utility::convertNumberToWords($grandTotal);

            $remarks = '';

            if(isset($products[0]->remarks))
            {
                 $remarks = $products[0]->remarks;
            }
              if(isset($lewhInfo->authorized_by) && !empty($lewhInfo->authorized_by)){
                $remarks .= ($remarks!='')?'\n'.$lewhInfo->authorized_by: $lewhInfo->authorized_by;
              }
              // $resultArray['remarks'] = $remarks;              
              $masvalue = $this->_masterLookup->getMasterLokup(78018);
              $termsdisplay = isset($masvalue->description)?$masvalue->description:0;
              $resultArray['is_display_terms'] = $termsdisplay;
              // $resultArray['terms_conditions'] = ($termsdisplay)?Lang::get('terms_and_conditions.Conditions'):"";
              $resultArray['order_type'] = isset($orderDetails->payment_method)?$orderDetails->payment_method:'';
              $resultArray['order_code'] = $orderDetails->order_code;
              if(isset($lewhInfo->jurisdiction) && !empty($lewhInfo->jurisdiction))
                  $resultArray['jurisdiction'] = $lewhInfo->jurisdiction;
              $resultArray['authorized'] = $lewhInfo->authorized_by;
              $resultArray['authorized'] = str_replace('#logo', '', $resultArray['authorized']);
              $resultArray['authorized'] = trim($resultArray['authorized']);
              $resultArray['business_legal_name'] = $leInfo->business_legal_name;
              $resultArray['logo'] = 'http://portal.ebutor.com/assets/admin/layout/img/small-logo.png';

//            $finalOutput .= "\n";

            $finalResultArray[] = $resultArray;
        
          }
        
         return Response::json(array('status' => 'success','data'=>$finalResultArray));

        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function printDeliveryChallan() {

        try
        {

           $data = Input::all();
           $resultArray = array();
           $finalResultArray = array();

           $postData = json_decode($data['data'], true);
           $orderIds = explode(',',$postData['orderIds']);

            
            foreach($orderIds as $orderId) {
            $products = $this->_orderModel->getInvoiceProductByOrderId($orderId);

            if(isset($products[0]->gds_order_id)){
                $orderId = $products[0]->gds_order_id;
                $orderDetails = $this->_orderModel->getOrderDetailById($orderId);

                $trackInfo = $this->_OrderTrack->getTrackDetailByOrderId($orderId);
                $taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);
                $billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
                $billingAndShipping = $this->convertBillingAndShippingAddress($billingAndShippingArr);
                $legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id,$orderDetails->le_wh_id);

                $taxSummaryArr = $this->_orderModel->getTaxSummary($taxArr);
                $taxSummary = isset($taxSummaryArr['summary']) ? $taxSummaryArr['summary'] : '';
                $productTaxArr = isset($taxSummaryArr['item']) ? $taxSummaryArr['item'] : '';
                $taxBreakup = isset($taxSummaryArr['breakup']) ? $taxSummaryArr['breakup'] : '';
                //echo '<pre>';print_r($orderDetails);die;
                $leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
                $lewhInfo = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);
                
                $prodTaxes = array();
                
                foreach ($taxArr as $tax) {
                    $prodTaxes[$tax->product_id] = array('name'=>$tax->name, 'tax_value'=>$tax->tax_value, 'tax'=>$tax->tax);
                }
                $companyInfo = $this->_leModel->getCompanyAccountByLeId($orderDetails->legal_entity_id);
                $userInfo = '';
                if($orderDetails->created_by) {
                    $userInfo = $this->_leModel->getUserById($orderDetails->created_by);
                }

                $delSlots = $this->_masterLookup->getMasterLookupByCategoryName('Delivery Slots');                    
                $cratesList = $this->_orderModel->getContainerInfoByOrderId($orderId);
                $pickerInfo = $this->_OrderTrack->getPickerByOrderId($orderId);            
            }

                $printString= '';
            if(isset($leInfo) && isset($lewhInfo)) {
                $printString.= $leInfo->business_legal_name."\n";
                $printString.= $lewhInfo->address1.", ";
                $printString.= (empty($lewhInfo->address2) ? '' : $lewhInfo->address2.", ");
                $printString.= $lewhInfo->city.", ";
                $printString.= $lewhInfo->state_name.", ";
                $printString.= (empty($lewhInfo->country_name) ? 'India, ' : $lewhInfo->country_name.", ");
                $printString.= $lewhInfo->pincode."\n";
                $printString.= "<b>TIN No:</br> ".$lewhInfo->tin_number;
            }

            $resultArray['Address1'] = $printString;

            /**
             * Shipping address of retailer
             */
                                    
            $shipping = $billingAndShipping['shipping'];

            $shippingArr = ucwords($orderDetails->shop_name)."\n";
            $shippingArr = $shippingArr."".$shipping->fname." ".$shipping->mname." ".$shipping->lname."\n";
            $shippingArr = $shippingArr."".$shipping->addr1." ".$shipping->addr2."\n";

            if(!empty($shipping->locality)) {
                $shippingArr = $shippingArr."".$shipping->locality.", ";
            }
            
            if(!empty($shipping->landmark)) {
                $shippingArr = $shippingArr."".$shipping->landmark.", ";
            }
            
            if(!empty($shipping->city)){
                $shippingArr = $shippingArr."".$shipping->city.", ";
            }

            $shippingArr = $shippingArr."".$shipping->state_name.", ".$shipping->country_name.", ".$shipping->postcode."\n";

            $shippingArr = $shippingArr."Telephone: ".$shipping->telephone."\n";
            
            if(!empty($shipping->mobile)) {
                $shippingArr = $shippingArr."Mobile: ".$shipping->mobile.", ";
            }
            
            if(!empty($orderDetails->beat)) {
                $shippingArr = $shippingArr."Beat: ".$orderDetails->beat."\n";
            }
            
            // if(!empty($orderDetails->areaname)) {
            //     $shippingArr = $shippingArr."Area: ".$orderDetails->areaname."\n";
            // }

            $outputshippingArr = wordwrap($shippingArr,29,"\n");

            $resultArray['Address2'] = $shippingArr;
            /**
             * Invoice details
             */
            
            $invoiceArr = "SO No: ".$orderDetails->order_code."\n";
            $invoiceArr = $invoiceArr."Date: 30-06-2017"."\n";

            if(!empty($lewhInfo->le_wh_code)){
                $invoiceArr = $invoiceArr."DC No: ".$lewhInfo->le_wh_code."\n";
            }
            
            if(!empty($lewhInfo->le_wh_name)){
                $invoiceArr = $invoiceArr."DC Name: ".$lewhInfo->le_wh_name."\n";
            }
            
            if(!empty($orderDetails->hub_name)){
                $invoiceArr = $invoiceArr."Hub Name: ".$orderDetails->hub_name."\n";
            }
            
            if(isset($userInfo->firstname) && isset($userInfo->lastname)){
                $invoiceArr = $invoiceArr."Created By: ".$userInfo->firstname." ".$userInfo->lastname."\n";
            }
            
            if(isset($userInfo->mobile_no)){
                $invoiceArr = $invoiceArr."(M: ".$userInfo->mobile_no.")\n";
            }
            
            if(isset($pickerInfo->firstname) && isset($pickerInfo->lastname)){
                $invoiceArr = $invoiceArr."Picked By: ".$pickerInfo->firstname." ".$pickerInfo->lastname."\n";
            }
           
            if(isset($delSlots[$orderDetails->pref_slab1]) && $delSlots[$orderDetails->pref_slab1]!=''){
                $invoiceArr = $invoiceArr."Del Slot1: ".$delSlots[$orderDetails->pref_slab1]."\n";
            }
            
            if(isset($delSlots[$orderDetails->pref_slab2]) && $delSlots[$orderDetails->pref_slab2]!=''){
                $invoiceArr = $invoiceArr."Del Slot2: ".$delSlots[$orderDetails->pref_slab2]."\n";
            }

            $resultArray['Address3'] = $invoiceArr;

            $productArray = array();

            /**
             * Initializations for the variables of product details
             */
            
            $sno              = 0;
            $x                = 0;
            $sub_total        = 0;
            $total_qty        = 0;
            $InvoicedQty      = 0;
            $total_inv_cfc    = 0;
            $total_unit_price = 0;
            $total_mrp        = 0;
            $total_net        = 0;
            $total_discount   = 0;
            $total_tax        = 0;
            $total_tax_value  = 0;

            $tax            = 0;
            $discount       = 0;
            $shippingAmount = 0;
            $otherDiscount  = 0;
            $grandTotal     = 0;
            $totInvoicedQty = 0;
            $finalTaxArr    = array();

            $printSkuString = '';
            foreach($products as $product)
            {
                $sno = ++$x;
                $taxName   = (isset($prodTaxes[$product->product_id]['name']) ? $prodTaxes[$product->product_id]['name'] : 0);
                $taxPer    = (isset($prodTaxes[$product->product_id]['tax']) ? $prodTaxes[$product->product_id]['tax'] : 0);
                $tax_value = (isset($prodTaxes[$product->product_id]['tax_value']) ? $prodTaxes[$product->product_id]['tax_value'] : 0);

                $singleUnitPrice = (($product->total / (100 + $taxPer) * 100) / $product->qty);
                
                $unitPrice = ($singleUnitPrice * $product->invoicedQty);
                $taxValue  = (($singleUnitPrice * $taxPer) / 100) * $product->invoicedQty;
                $netValue  = ($singleUnitPrice * $product->invoicedQty);
                $subTotal  = $taxValue + $netValue;
                $discount  = 0;
                $taxkey    = $taxName . '-' . $taxPer;
                if ($taxkey != '0-0') {
                    $finalTaxArr[$taxkey][] = array(
                        'tax' => $taxPer,
                        'name' => $taxName,
                        'qty' => $product->qty,
                        'tax_value' => $tax_value,
                        'taxamtPer' => ($tax_value / $product->qty),
                        'taxamt' => (($tax_value / $product->qty) * $product->invoicedQty)
                    );
                }

                                

                  $taxArr = number_format($taxValue, 2);
                
                
                $sub_total      = $sub_total + $subTotal;
                $total_discount = $total_discount + $discount;
                $total_net      = $total_net + $netValue;
                $total_qty      = $total_qty + $product->qty;
                $InvoicedQty    = $InvoicedQty + $product->invoicedQty;
                $total_inv_cfc  = $total_inv_cfc + $product->invCfc;
                $total_tax      = $total_tax + $taxValue;

                 $productArray[] = array('name'=>$product->pname,
                                          'mrp'=> number_format((float)$product->mrp,2),
                                          'up'=>number_format($singleUnitPrice, 2),
                                          'oq'=>(int)$product->qty,
                                          'iq'=>(int)$product->invoicedQty,
                                          'invcfc'=>number_format($product->invCfc, 2),
                                          'net'=>number_format($netValue, 2),
                                          'taxper'=>number_format($taxPer, 2),
                                          'tax'=>$taxArr,
                                          'sd'=>number_format($discount, 2),
                                          'subtot'=>number_format($subTotal, 2)
                                          );


            }
            
            $resultArray['Products'] = $productArray;

    
            $totalArray = array('oq'=>$total_qty,
                                'iq'=>$InvoicedQty,
                                'invcfc'=>number_format($total_inv_cfc, 2),
                                'net'=>number_format($total_net, 2),
                                'tax'=>number_format($total_tax, 2),
                                'sd'=>number_format($total_discount, 2),
                                'subtot'=>number_format($sub_total, 2)              
                                );    
            $resultArray['totalArray'] = $totalArray;


            // The Entire Code Below is about VAT Tax
            $finalNewTaxArr = array();
            foreach ($finalTaxArr as $key => $taxArr) {
                $finalNewTaxArr[$key] = array();
                $totAmt               = 0;
                foreach ($taxArr as $tax) {
                    $totAmt                       = $totAmt + $tax['taxamt'];
                    $finalNewTaxArr[$key]['name'] = $tax['name'];
                    $finalNewTaxArr[$key]['tax']  = $tax['tax'];
                }
                $finalNewTaxArr[$key]['tax_value'] = $totAmt;
            }

            if(isset($finalNewTaxArr) && is_array($finalNewTaxArr))
            {
                foreach($finalNewTaxArr as $tax)
                    $vat = $tax['name']." ".(isset($tax['tax']) ? (float)$tax['tax'] : 0).'%';
            }

            $totInvQty = $InvoicedQty;
            $subTotal =  number_format($sub_total, 2);
            $shippingAmt =  0;
            $totSchDisc =  number_format($total_discount, 2);
            $otherDisc =  number_format($orderDetails->discount, 2);
            $totDisc =  number_format(($total_discount + $orderDetails->discount), 2);
            
            $vat_5 = "-----";
            $vat_14 = "--------";
            if(isset($finalNewTaxArr) && is_array($finalNewTaxArr))                                     
            {
                foreach($finalNewTaxArr as $tax)
                {
                    if((int)$tax['tax'] == 5)
                        $vat_5= number_format((isset($tax['tax_value']) ? ($tax['tax_value']) : 0), 2);
                    if((int)$tax['tax'] == 14)
                        $vat_14= number_format((isset($tax['tax_value']) ? ($tax['tax_value']) : 0), 2);
                }
                
            }
            $totalTax = number_format($total_tax, 2);
            
            $grandTotal          = $sub_total;
            $grandTotalWithRound = Utility::getRoundOff($grandTotal, 'gtround');
            $roundoff            = Utility::getRoundOff($grandTotal, 'roundoff');
            
            $roundOff = number_format($roundoff, 2);
            $grandTotal = number_format($grandTotalWithRound, 2);

            //// TIQ = 5, ST = 8, SA = 10, TSD = 9, OD = 6, TD = 5, V5= 5, v14 = 8, TT = 4, ROff = 6, GT = 12

            $resultArray['totalArray'] = $totalArray;

            $vatArray = array('invqty'=>$totInvQty,
                              'subtot'=>$subTotal,
                              'ship'=>$shippingAmt,
                              'shdisc'=>$totSchDisc,
                              'othdisc'=>$otherDisc,
                              'totdisc'=>$totDisc,
                              'vat5'=>$vat_5,
                              'vat14'=>$vat_14,
                              'tottax'=>$totalTax,
                              'roundOff'=>$roundOff,
                              'grandTot'=>$grandTotal  
                              );
            $resultArray['vatArray'] = $vatArray;



            $trackArray = array();

            $resultArray['trackArray'] = array('cfc'=>0,
                                                'cfc_list'=>'',
                                                'crates'=>0,
                                                'crates_list'=>'',
                                                'bags'=>0,
                                                'bags_list'=>'');

            $cratesList = $this->_orderModel->getContainerInfoByOrderId($orderId);

            if(isset($cratesList['16004'])) {
                $resultArray['trackArray']['cfc_list'] = $cratesList['16004']; 
            }
            if(isset($cratesList['16006'])) {
                $resultArray['trackArray']['bags_list'] = $cratesList['16006']; 
            }
            if(isset($cratesList['16007'])) {
                $resultArray['trackArray']['crates_list'] = $cratesList['16007']; 
            }


            if(isset($trackInfo))
            {                                
                if($trackInfo->cfc_cnt!=0)                    
                {                    
                    $resultArray['trackArray']['cfc'] = (int)$trackInfo->cfc_cnt;
                }               
                // Bags                
                if($trackInfo->bags_cnt!=0)
                {                    

                    $resultArray['trackArray']['bags'] = (int)$trackInfo->bags_cnt;

  
                }                
                //Crates                
                if($trackInfo->crates_cnt!=0)                
                {                    
                    
                    $resultArray['trackArray']['crates'] = (int)$trackInfo->crates_cnt;

                }            
            }

//            $finalOutput .= "\n";

            $finalResultArray[] = $resultArray;
        
          }
        
         return Response::json(array('status' => 'success','data'=>$finalResultArray));

        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }


    public function convertBillingAndShippingAddress($billingAndShippingArr) {
        try{
                $billingAndShipping = array();
                if(isset($billingAndShippingArr) && is_array($billingAndShippingArr) && count($billingAndShippingArr) > 0) {
                    foreach($billingAndShippingArr as $billingAndShippingData) {
                        if($billingAndShippingData->address_type == 'shipping') {
                            $billingAndShipping['shipping'] = $billingAndShippingData;
                        }

                        if($billingAndShippingData->address_type == 'billing') {
                            $billingAndShipping['billing'] = $billingAndShippingData;
                        }
                    }
                
                    if(count($billingAndShipping)==1) {

                        $billingAndShipping['billing'] = $billingAndShipping['shipping'];                       

                    }
                }
                return $billingAndShipping;
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getOrdersByHub() {
        try
        {

           $data = Input::all();
           $postData = json_decode($data['data'], true);
           
           $hubId = $postData['hub_id']; 

           $beatIds = (isset($postData['beat_ids']) && !empty($postData['beat_ids'])) ? explode(',',$postData['beat_ids']) : [];


           $filters = array('start_date'=>'','end_date'=>'','beat_ids'=>'');

           if(isset($postData['start_date']) && isset($postData['end_date'])) {
               $filters = array('start_date'=>$postData['start_date'],'end_date'=>$postData['end_date'],'beat_ids'=>$beatIds);
           }
            if(isset($postData['status']) && $postData['status']=='sit') {
                $filters['status'] = 'sit';
            } else {
                $filters['status'] = 'invoice';
            }
            
            $data = $this->_dotmatrixModel->getOrderInfo($hubId,$filters);
            return Response::json(array('status' => 'success','data'=>$data));
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    public function getHubsList() {
        try
        {


            $data=isset($_POST['data'])?$_POST['data']:'';
            if($data!=''){
              $data=json_decode($data,1);
              $user_id=$data['user_id'];
              if($user_id!=''){
                $data = $this->_dotmatrixModel->getHubsList($user_id);
                return Response::json(array('status' => 'success','data'=>$data));
              }else{
                return 0;
              }
            }else{
              return 0;
            }
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    public function getBeatsByHub() {
        try
        {


            $data=isset($_POST['data'])?$_POST['data']:'';
            if($data!=''){
              $data=json_decode($data,1);
              $hub_id=isset($data['hub_id']) ? $data['hub_id'] : '';
              $flag=isset($data['flag'])?$data['flag']:'';              
              if($hub_id!=''){
                $data = $this->_dotmatrixModel->getBeatsByHub($hub_id,$flag);
                if($data){                
                  return Response::json(array('status' => 'success','message'=>'success','data'=>$data));
                }else{
                  return Response::json(array('status' => 'success','message'=>'success','data'=>[]));
                }
              }else{
                  return Response::json(array('status' => 'failed','message'=>'hub_id required','data'=>[]));
              }
            }else{
                  return Response::json(array('status' => 'failed','message'=>'input is required','data'=>[]));
            }
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    public function updateInvoicePrintStatus() {
        try {


            $data = Input::all();
            $postData = json_decode($data['data'], true);
           
            $orderIds = explode(',',$postData['orderIds']); 

            if(count($orderIds)>0) {

                $data = $this->_dotmatrixModel->updateInvoicePrintStatus($orderIds);

                return Response::json(array('status' => 200, 'message' => 'Success', 'data'=>$data));

            } else {


                return Response::json(array('status' => 400, 'message' => 'OrderIds were missing', 'data'=>''));

            }

        } catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function mobileInvoicePDF($id){
      $orderId=$id;
      $this->_InvoiceModel = new Invoice();
      $invoiceInfo = $this->_InvoiceModel->getInvoiceGridOrderId(array($orderId), array('grid.gds_invoice_grid_id','grid.ecash_applied'));
      $invoiceId = $invoiceInfo[0]->gds_invoice_grid_id;
      $invoice_type=1;
      $invoiceCls = new InvoiceController(1);
      return  $invoiceCls->printInvoiceAction($invoiceId,$orderId,$invoice_type);
    } 
    public function bulkInvoiceApi(Request $request,$flag){
      $details = $request->all();
     if(isset($details)){
        $token = isset($details['token'])?$details['token']:"";
        if($token != ""){
          $tokenId = $this->_dotmatrixModel->getCheckingToken($token);
          if (count($tokenId)) {
               $orderIds = explode(',',$details['orderIds']);
               $invoiceCls = new OrdersController(1);
               Session::set('orderIds',array($orderIds));
               return  $invoiceCls->printBulkInvoiceAction($flag);
          }else{
            return array("data"=>[],"message"=>"Invalid Token","status"=>"falied");
          }
        }else{
          return array("data"=>[],"message"=>"Please send Token","status"=>"falied");
        }


     }else{
        return array("data"=>[],"message"=>"Invalid data","status"=>"falied");
     }
   }
}