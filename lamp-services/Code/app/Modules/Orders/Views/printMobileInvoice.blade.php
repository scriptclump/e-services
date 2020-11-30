<html dir="ltr" lang="en">
    <head>
        <style>
            table {
                border-collapse: collapse;
            }
            .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, /*.table-bordered>tfoot>tr>td,*/ /*.table-bordered>tfoot>tr>th,*/ .table-bordered>thead>tr>td, .table-bordered>thead>tr>th{padding:5px; background: white !important;}
            .table-bordered>tbody>tr>td{border: 1px solid #000 !important;}
            .page-break{ display: block !important; clear: both !important; page-break-after:always !important;}
            .table-headings th{background:#c0c0c0 !important; font-weight:bold !important; border:1px solid #000 !important;}
        </style>

    </head>
    <body>
        <div class="container">
            <div class="row">
                <center><h6>TAX INVOICE</h6></center>
                <div class="col-md-12">
                    <table width="100%" border="0" cellspacing="5" cellpadding="5">
                        <tr>
                            <td width="100%" align="left" valign="top">
                                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        
                                            @if(is_object($leInfo) and $leInfo->logo != "" and $leInfo->logo != "null")

                                                <img src="{{$leInfo->logo}}" alt="Image" height="42" width="42" >

                                            @endif
                                              <td><strong style="padding-top:-20px; font-size: 9px;">@if(is_object($leInfo)){{$leInfo->business_legal_name}}@endif</strong></td>
                                        
                                @if(isset($lewhInfo->authorized_by) && !empty($lewhInfo->authorized_by))
                                <tr>
                                <td style="width:100%;"><?php echo $lewhInfo->authorized_by ?>
                               
                                </tr>     
                               @endif
                                      
                                    </tr>
                                </table>

                            </td>
                            <td width="50%" align="right" valign="middle"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="float:right;font-size:9px;padding:0 0 0 0;"><strong>ORDER TYPE: </strong>{{$orderDetails->payment_method}}</div>
            <div class="row" style="margin:4px 0px;">

            </div>

            <div class="row">
                <div class="col-md-12">
                    <table width="100%" class="table table-bordered thline printtable " cellpadding="1" style="font-weight: bold;">
                        <tr style="font-size:9px; text-align:left" class="hedding1 table-headings">
                            <th width="25%" >Details of Supplier</th>
                            <th width="25%" >Receiver (Billed To & Shipped To)</th>
                            <th width="25%" >Invoice Details</th>
                        </tr>
                        <tr>
                            <td valign="top" style="font-size:9px;">

                                @if(is_object($lewhInfo))
                                <strong>Name:</strong> {{$leInfo->business_legal_name}}
                                <br>
                                <strong>Address:</strong> {{$lewhInfo->address1}}{{(empty($lewhInfo->address2) ? '' : ', '.$lewhInfo->address2.',')}}
                                <br>
                                {{$lewhInfo->city}}, {{$lewhInfo->state_name}}, {{(empty($lewhInfo->country_name) ? 'India' : $lewhInfo->country_name)}}, {{$lewhInfo->pincode}}
                                </br><strong>State Code:</strong> {{$lewhInfo->state_code}}
                                @if(!empty($lewhInfo->tin_number)) 
                                </br><strong>GSTIN:</strong> {{$lewhInfo->tin_number}}
                                @endif
                                @if(isset($lewhInfo->fssai) && !empty($lewhInfo->fssai)) 
                                </br><strong>FSSAI No:</strong> {{$lewhInfo->fssai}}
                                @endif</div>@endif

                            </td>
                            <td valign="top" style="font-size:9px;">

                                <strong>Name:</strong> {{ucwords($orderDetails->shop_name)}}<br>

                                {{$orderDetails->firstname}} {{$orderDetails->lastname}} <br>
                                @if(is_object($billing))   
                                {{$billing->addr1}} {{$billing->addr2}},<br>
                                @if(!empty($billing->locality)) {{$billing->locality}}, @endif @if(!empty($billing->landmark)){{$billing->landmark}}, @endif {{$billing->city}}, {{$billing->state_name}}, {{$billing->country_name}}, {{$billing->postcode}}, {{$billing->state_code}}<br>
                                <strong>Telephone:</strong> {{$orderDetails->phone_no}}
                                @if($orderDetails->legal_entity_type_id !=1014 && $orderDetails->legal_entity_type_id !=1016) 
                                    @if(!empty($orderDetails->beat))<strong>Beat:</strong> {{$orderDetails->beat}}@endif
                                @endif
                                @if(!empty($billing->gstin)) 
                                @if(!is_null($billing->gstin)) 
                                <strong>GSTIN / UIN:</strong> {{$billing->gstin}}
                                @else
                                <strong>GSTIN / UIN:</strong> N/A
                                @endif
                                @endif
                                @endif                  
                            </td>

                            <td valign="top" style="font-size:9px;">
                                <strong>Invoice No:</strong> {{isset($products[0]->invoice_code) ? $products[0]->invoice_code : $products[0]->gds_invoice_grid_id}}<br>
                                <strong>Invoice Date:</strong> {{date('d-m-Y h:i A', strtotime($products[0]->invoice_date))}}<br>
                                <strong>SO No.</strong> {{$orderDetails->order_code}} <br/>
                                <strong>Date:</strong> {{date('d-m-Y h:i A', strtotime($orderDetails->order_date))}}<br>
                                <strong>DC Name:</strong> {{$lewhInfo->lp_wh_name}}<br>
                                @if(isset($userInfo->firstname) && isset($userInfo->lastname))
                                <strong>SO Name:</strong> {{$userInfo->firstname}} {{$userInfo->lastname}} <strong>(M:</strong> {{isset($userInfo->mobile_no) ? $userInfo->mobile_no : ''}}<strong>)</strong>
                                @endif

                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- @include('Orders::AllAmountInRupees') -->
          <!--   <br> -->
          <div class="" style="height: 2px;">
              
          </div>
            <div class="row">
                <div class="col-md-12">
                   <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered table-advance table-hover" style="font-size:9px;font-weight: bold;">
                        <thead>
                        <tr class="hedding1 table-headings" >
                            <th align="left" rowspan="2">SNO</th>
                            <th align="left" rowspan="2">Product Name</th>
                            <th align="left" rowspan="2">HSN Code</th>
                            <th align="right" rowspan="2">MRP</th>
                            <th align="right" rowspan="2">Rate</th>
                            <th align="centre" rowspan="2">Qty</th>
                            @if($orderDetails->discount_before_tax == 1)
                            <th align="centre" rowspan="2">Cost</th>
                            <th align="centre" rowspan="2">Disc.</th>
                            @endif
                            <th align="right" rowspan="2">Taxable<br>Value</th>
                            <th align="right" rowspan="2">Tax <br>Rate</th>
                            <th align="right" rowspan="2">Tax<br> Amt</th>
                            <th colspan="2">CGST</th>
                            <th colspan="2">SGST/UTGST</th>
                            <th colspan="2">IGST</th>
                            @if($orderDetails->discount_before_tax == 0)
                            <th rowspan="2">Disc.</th>
                            @endif
                            <th rowspan="2">Total</th>
                        </tr>
                        <tr class="hedding1 table-headings">
                            <th colspan="1" rowspan="1" style="border-right:1px solid;">%</th>
                            <th colspan="1" rowspan="1">Amt</th>
                            <th colspan="1" rowspan="1" style="border-right:1px solid;">%</th>
                            <th colspan="1" rowspan="1" style="border-right:1px solid;">Amt</th>
                            <th colspan="1" rowspan="1">%</th>
                            <th colspan="1" rowspan="1">Amt</th>
                        </tr>
                        </thead>

                        <?php
                        $sno = 1;
                        $sub_total = 0;
                        $total_qty = 0;
                        $InvoicedQty = 0;
                        $total_unit_price = 0;
                        $total_net = 0;
                        $total_discount = 0;
                        $total_tax = 0;

                        $tax = 0;
                        $discount = 0;
                        $shippingAmount = 0;
                        $otherDiscount = 0;
                        $grandTotal = 0;
                        $totInvoicedQty = 0;
                        $finalTaxArr = array();

                        $totCGST = $totSGST = $totIGST = $totUTGST = 0;
                        $totalTaxableValue = 0;
                        ?>
                        @foreach($products as $product)
                        <tr>
                            <?php
                            $taxName = (isset($prodTaxes[$product->product_id]['name']) ? $prodTaxes[$product->product_id]['name'] : 0);
                            $taxPer = (isset($prodTaxes[$product->product_id]['tax']) ? $prodTaxes[$product->product_id]['tax'] : 0);
                            $tax_value = (isset($prodTaxes[$product->product_id]['tax_value']) ? $prodTaxes[$product->product_id]['tax_value'] : 0);


                            $singleUnitPrice = (($product->total / (100 + $taxPer) * 100) / $product->qty);
                            $unitPrice = ($singleUnitPrice * $product->invoicedQty);
                            $taxValue = (($singleUnitPrice * $taxPer) / 100 ) * $product->invoicedQty;
                            $netValue = ($singleUnitPrice * $product->invoicedQty);
                            $subTotal = $taxValue + $netValue;
                            $discount = $product->discount_amt;
                            
                            if($orderDetails->discount_before_tax == 1){
                                $singleUnitPrice = (($product->cost) / $product->qty);
                                $discount = ($singleUnitPrice*$product->invoicedQty*$product->discount)/100;
                            }
                            $taxkey = $taxName.'-'.$taxPer;
                            
                            if($taxkey != '0-0') {
                              $finalTaxArr[$taxkey][] = array('tax'=>$taxPer, 'name'=>$taxName, 'qty'=>$product->qty, 'tax_value'=>$taxValue, 'taxamt'=>$taxValue);
                            }

                            $totCGST = $totCGST + $product->CGST;
                            $totSGST = $totSGST + $product->SGST;
                            $totIGST = $totIGST + $product->IGST;
                            $totUTGST = $totUTGST + $product->UTGST;

                            $cgstPer = isset($prodTaxes[$product->product_id]['cgstPer']) ? $prodTaxes[$product->product_id]['cgstPer'] : 0;
                            $sgstPer = isset($prodTaxes[$product->product_id]['sgstPer']) ? $prodTaxes[$product->product_id]['sgstPer'] : 0;
                            $igstPer = isset($prodTaxes[$product->product_id]['igstPer']) ? $prodTaxes[$product->product_id]['igstPer'] : 0;
                            $utgstPer = isset($prodTaxes[$product->product_id]['utgstPer']) ? $prodTaxes[$product->product_id]['utgstPer'] : 0;

                            $totalTaxableValue = $totalTaxableValue+$netValue;

                                      $sub = substr($product->cfcName,0,1);
        
                            ?>
                            <td width="1%">{{$sno}}</td>
                            
                            <td width="45%">{{$product->pname}}</td>
                            <td width="2px">{{$product->hsn_code}}</td>
                            <td width="2px" align="right">{{number_format($product->mrp, 2)}}</td>
                            <td width="2px" align="right">{{number_format($singleUnitPrice, 2)}}</td>
                            <td width="7px" align="centre"> {{(int)$product->invoicedQty}} ({{round($product->invCfc, 2)}} {{$sub}})</td>
                            <!--<td align="right">{{number_format($product->invCfc, 2)}}</td>-->
                            @if($orderDetails->discount_before_tax == 1)
                            <td width="2px"align="right">{{number_format($singleUnitPrice*$product->invoicedQty, 2)}}</td>
                            <td width="1px" align="right">{{number_format($discount, 2)}}</td>                            
                            @endif
                            <td width="2px" align="right">{{number_format($netValue, 2)}}</td>
                            <td width="1px" align="right">{{(float)$taxPer}}</td>
                            <td width='1px' align="right">{{number_format($taxValue, 2)}}</td>
                            <td width="1px" align="right">{{$cgstPer}}</td>
                            <td width="2px" align="right">{{number_format($product->CGST, 2)}}</td>
                            @if($sgstPer!=0)
                            <td width="1px" align="right">{{$sgstPer}}</td>
                            <td width="4px" align="right">{{number_format($product->SGST, 2)}}</td>
                            @elseif($utgstPer!=0)
                            <td align="right" width="1px">{{$utgstPer}}</td>
                            <td align="right" width="1px">{{number_format($product->UTGST,2)}}</td>
                            @else
                            <td align="right" width="1px">0</td>
                            <td align="right" width="1px">0</td>
                            @endif
                            <td width="1px"align="right">{{$igstPer}}</td>
                            <td width="2px" align="right">{{number_format($product->IGST, 2)}}</td>
                            @if($orderDetails->discount_before_tax == 0)
                            <td width="1px" align="right">{{number_format($discount, 2)}}</td>
                            @endif
                            
                            <td width="2px" align="right">{{number_format($subTotal, 2)}}</td>
<?php

$netValue = ($singleUnitPrice * $product->invoicedQty);
$sub_total = $sub_total + $subTotal;
$total_discount = $total_discount + $discount;
$total_net = $total_net + $netValue;
$total_qty = $total_qty + $product->qty;
$InvoicedQty = $InvoicedQty + $product->invoicedQty;
$total_tax = $total_tax + $taxValue;
$sno = $sno + 1;
?>
                        </tr>
                        @endforeach
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp; </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right"><strong>Total:</strong></td>
                            <td align="right"><strong>{{$InvoicedQty}}</strong></td>
                            <td align="right"><strong>{{number_format($total_net, 2)}}</strong></td>
                            
                            @if($orderDetails->discount_before_tax == 1)
                            <td align="right"><strong>{{number_format($total_discount, 2)}}</strong></td>
                            <td align="right"><strong>{{number_format($totalTaxableValue, 2)}}</strong></td>
                            @endif

                            <td align="right"></td>
                            <td align="right"><strong>{{number_format($total_tax, 2)}}</strong></td>
                            <td>&nbsp;</td>
                            <td align="right"><strong>{{number_format($totCGST, 2)}}</strong></td>
                            @if($totSGST !=0)
                                <td>&nbsp;</td>
                                <td align="right"><strong>{{number_format($totSGST, 2)}}</strong></td>
                            @elseif($totUTGST !=0)
                                 <td align="right"></td>
                                 <td align="right"><strong>{{number_format($totUTGST, 2)}}</strong></td>
                             @else
                                <td align="right">0</td>
                                <td align="right">0</td>
                            @endif
                            <td>&nbsp;</td>
                            <td align="right"><strong>{{number_format($totIGST, 2)}}</strong></td>
                            
                            @if($orderDetails->discount_before_tax == 0)
                            <td align="right"><strong>{{number_format($total_discount, 2)}}</strong></td>
                            @endif
                            <td align="right"><strong>{{number_format($sub_total, 2)}}</strong></td>
                        </tr>
                        
<?php


if ($totSGST > 0 || $totCGST > 0 || $totIGST > 0) {
    $gstData = array('CGST' => $totCGST, 'SGST' => $totSGST, 'IGST' => $totIGST);
} else {
    $gstData = array();
}
$bill_discount = isset($products[0]->bill_disc_amt)?$products[0]->bill_disc_amt:0;
?>        
                    </table>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-12">
                    <table cellpadding="1" cellspacing="1" class="table table-striped table-bordered table-advance table-hover" style="width:100%;font-size:9px; float: right;margin-top: 1px;font-family: Arial;">

                        <tr class="hedding1 table-headings">
<?php
$grandTotal = ($sub_total-$bill_discount);
$grandTotal = $grandTotal-$ecash_applied;
$grandTotalWithRound = Utility::getRoundOff($grandTotal, 'gtround');
$roundoff = Utility::getRoundOff($grandTotal, 'roundoff');
?>                          
                            <th style="border: 0px !important;background: white !important;width:40% padding-left: 2px;" align="left">Grand Total In Words: <?php echo Utility::convertNumberToWords($grandTotalWithRound); ?>
                            </th>
                            <th align="right">E-Cash Applied</th>
                            <th align="right">Round Off</th>
                            <th align="right">Grand Total</th>
                        </tr>
                      
                            <td style="border: 0px !important;background: white !important;">
                                @if(isset($lewhInfo->jurisdiction) && !empty($lewhInfo->jurisdiction))

                                    <font size= "1"><strong>Jurisdiction Only:</strong>{{$lewhInfo->jurisdiction}}</font>
                                @endif

                            </td>
                            <td align="right">{{number_format($ecash_applied, 2)}}</td>
                            <td align="right">{{number_format($roundoff, 2)}}</td>
                            <td align="right"><strong>{{number_format($grandTotalWithRound, 2)}}</strong></td>
                       </tr>
                    </table>                                        
                </div>
            </div>
             <footer style="float:right;font-size: 9px;margin: 5px;"><strong>Order ID</strong><strong>:</strong>{{$orderDetails->order_code}}</footer>
            <div class="page-break"></div>
        </div>
    </body>
</html>
