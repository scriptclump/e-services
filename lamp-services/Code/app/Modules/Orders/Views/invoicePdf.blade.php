<style>
body {
    margin: 0px;
    padding: 0px;
    color: #333;
    font-family: "Open Sans", sans-serif;
}
</style>
<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>
@if(is_object($leInfo) && is_object($lewhInfo))
<table width="100%" border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td width="50%" align="left" valign="middle">
        @if(is_object($leInfo) and $leInfo->logo != "" and $leInfo->logo != "null")
            <img src="{{$leInfo->logo}}" alt="Image" height="42" width="42" >
        @endif
        <strong>{{$leInfo->business_legal_name}}</strong></td>
    <td width="50%" align="right" valign="middle"><div style="padding-left:20px; padding-top:10px; font-size:12px; float:right;">{{$lewhInfo->address1}}, @if(!empty($lewhInfo->address2)){{$lewhInfo->address2}},<br>@endif
        {{$lewhInfo->city}}, {{$lewhInfo->state_name}}, {{empty($lewhInfo->country_name) ? 'India' : $lewhInfo->country_name}}, {{$lewhInfo->pincode}}
        </br>State Code : {{$lewhInfo->state_code}}
        <br>GSTIN No : {{$lewhInfo->tin_number}}
        <br>FSSAI No : {{$lewhInfo->fssai}}</td>
  </tr>
</table>
@endif
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center"><h4>INVOICE</h4></td>
    </tr>
</table>

<table width="100%" style="border:1px solid #ccc;font-size:10px !important;" cellspacing="0" cellpadding="2">
    <tr style="font-size:14px;background-color:#e7ecf1 !important; font-weight:bold;">
      <td height="30" width="33%" bgcolor="#e7ecf1">Customer</td>
      <td width="33%" bgcolor="#e7ecf1">Shipping Address</td>
      <td width="33%" bgcolor="#e7ecf1">Invoice Details</td>
    </tr>

    <tr>
      <td valign="top" style="border-right:1px solid #ccc; font-size:11px;">
          <strong>Name:</strong> {{$orderDetails->firstname}} {{$orderDetails->lastname}} <br>
            
            <strong>Billing Address</strong><br>
            @if(is_object($billing))   
              {{$billing->fname}} {{$billing->mname}} {{$billing->lname}} <br>
              {{$billing->addr1}} {{$billing->addr2}}<br>
              @if(!empty($billing->locality)) {{$billing->locality}}, @endif @if(!empty($billing->landmark)){{$billing->landmark}}, @endif
              <strong>Phone:</strong> {{$orderDetails->phone_no}} <br>
              @if(!empty($orderDetails->beat))<strong>Beat:</strong> {{$orderDetails->beat}}<br>@endif
              @if(!empty($orderDetails->areaname))<strong>Area:</strong> {{$orderDetails->areaname}}<br>@endif
              @endif
              <strong>State Code:</strong> {{$billing->state_code}} <br>
              @if(!is_null($billing->gstin)) 
              <br /><strong>GSTIN:</strong> {{$billing->gstin}}
              @endif
              @if(isset($billing->fssai) && !is_null($billing->fssai)) 
              <br /><strong>FSSAI No:</strong> {{$billing->fssai}}
              @endif
      </td>
      <td valign="top" style="border-right:1px solid #ccc; font-size:11px;">
              @if(is_object($shipping))
              {{$orderDetails->shop_name}}<br> 
                {{$shipping->fname}} {{$shipping->mname}} {{$shipping->lname}}<br>
                {{$shipping->addr1}} {{$shipping->addr2}}<br>
                @if(!empty($shipping->locality)) {{$shipping->locality}}, @endif @if(!empty($shipping->landmark)){{$shipping->landmark}}, @endif {{$shipping->city}}, {{$shipping->state_name}}, {{$shipping->country_name}}, {{$shipping->postcode}}<br>
                <strong>Telephone:</strong> {{$shipping->telephone}}&nbsp;<strong>Mobile:</strong> {{$shipping->mobile}}
                @endif
                <strong>State Code:</strong> {{$shipping->state_code}} <br>

              @if(!is_null($shipping->gstin)) 
              <br /><strong>GSTIN:</strong> {{$shipping->gstin}}
              @endif 
              @if(isset($orderDetails->fssai)&& !is_null($orderDetails->fssai)) 
              <br /><strong>FSSAI No:</strong> {{$orderDetails->fssai}}
              @endif 
            
      </td>
      <td valign="top" style="font-size:11px;">
              <strong>Invoice No:</strong> {{isset($products[0]->invoice_code) ? $products[0]->invoice_code : $products[0]->gds_invoice_grid_id}}<br>
              <strong>Invoice Date:</strong> {{date('d-m-Y h:i A', strtotime($products[0]->invoice_date))}}<br>
              <strong>SO No. / Date:</strong> {{$orderDetails->order_code}} / {{date('d-m-Y h:i A', strtotime($orderDetails->order_date))}}<br>
              @if(!empty($lewhInfo->le_wh_code)) <strong>DC No:</strong> {{$lewhInfo->le_wh_code}}<br> @endif
              <strong>DC Name:</strong> {{$lewhInfo->lp_wh_name}}<br>
              @if(!empty($orderDetails->hub_name)) <strong>Hub Name:</strong> {{$orderDetails->hub_name}}<br> @endif
              <strong>Jurisdiction Only:</strong> {{isset($lewhInfo->city)?$lewhInfo->city:''}}
              @if(isset($userInfo->firstname) && isset($userInfo->lastname))
              <br>      <strong>Created By</strong>: {{$userInfo->firstname}} {{$userInfo->lastname}} (M: {{isset($userInfo->mobile_no) ? $userInfo->mobile_no : ''}})
                    @endif
              @if(isset($pickerInfo->firstname) && isset($pickerInfo->lastname))
              <br><strong>Picked By </strong>: {{$pickerInfo->firstname}} {{$pickerInfo->lastname}}<br>
              @endif      
                @if(isset($delSlots[$orderDetails->pref_slab1]) && $delSlots[$orderDetails->pref_slab1]!='') <strong>Del Slot1:</strong> {{$delSlots[$orderDetails->pref_slab1]}}<br> @endif

                @if(isset($delSlots[$orderDetails->pref_slab2]) && $delSlots[$orderDetails->pref_slab2]!='') <strong>Del Slot2:</strong> {{$delSlots[$orderDetails->pref_slab2]}}<br> @endif                
              </td>
    </tr>
</table>

<strong style="font-size:13px !important; ">Product Description</strong>

<table cellspacing="0" cellpadding="3" width="100%" style="margin-top:10px; border:1px solid #ccc; font-size:11px;">
          <tr style="font-weight:bold;">
            <td bgcolor="#e7ecf1" style="height:30px;">Product Name</td>
            <td bgcolor="#e7ecf1">HSN Code</td>
            <td bgcolor="#e7ecf1">MRP(Rs.)</th>
            <td bgcolor="#e7ecf1">Unit(Rs.)</td>
            <td bgcolor="#e7ecf1">Qty</td>
            <td bgcolor="#e7ecf1">Inv CFC</td>
            <td bgcolor="#e7ecf1">Tax %</td>
            <td bgcolor="#e7ecf1" align="right">Tax Amt(Rs.)</td>
            <td bgcolor="#e7ecf1" align="right">Net Amt(Rs.)</td>
            <td bgcolor="#e7ecf1" align="right">Sch. Disc.</td>
            <td bgcolor="#e7ecf1" align="right">Total(Rs.)</td>
          </tr>
@if(is_array($products))          
            <?php
$sno = 1;
$sub_total = 0;
$total_qty = 0;
$InvoicedQty = 0;
$total_unit_price = 0;
$total_mrp = 0;
$total_net = 0;
$total_discount = 0;
$total_tax = 0;
$total_tax_value = 0;

$tax = 0;
$discount = 0;
$shippingAmount = 0;
$otherDiscount = 0;
$grandTotal = 0;
$totInvoicedQty = 0;
$finalTaxArr = array();
$totCGST = $totSGST = $totIGST = $totUTGST = 0;
?>
            @foreach($products as $product)
            <?php
//print_r($product);            
$taxName = (isset($prodTaxes[$product->product_id]['name']) ? $prodTaxes[$product->product_id]['name'] : 0);
$taxPer = (isset($prodTaxes[$product->product_id]['tax']) ? $prodTaxes[$product->product_id]['tax'] : 0);
$tax_value =  (isset($prodTaxes[$product->product_id]['tax_value']) ? $prodTaxes[$product->product_id]['tax_value'] : 0);


$singleUnitPrice = (($product->total / (100+$taxPer)*100) / $product->qty);

$unitPrice = ($singleUnitPrice * $product->invoicedQty);
$taxValue = (($singleUnitPrice * $taxPer) / 100 ) * $product->invoicedQty;
$netValue = ($singleUnitPrice * $product->invoicedQty);;
$subTotal = $taxValue + $netValue;
$discount = 0;
$taxkey = $taxName.'-'.$taxPer;
if($taxkey != '0-0') {
  $finalTaxArr[$taxkey][] = array('tax'=>$taxPer, 'name'=>$taxName, 'qty'=>$product->qty, 'tax_value'=>$tax_value, 'taxamtPer'=>($tax_value/$product->qty), 'taxamt'=>(($tax_value/$product->qty)*$product->invoicedQty));
}

$totCGST = $totCGST + $product->CGST;
$totSGST = $totSGST + $product->SGST;
$totIGST = $totIGST + $product->IGST;
$totUTGST = $totUTGST + $product->UTGST;

?>
            <tr>
            <td style="border-bottom:1px solid #ccc;height:30px;">{{$product->pname}} (<strong>SKU:</strong> {{$product->sku}})</td>
            <td style="border-bottom:1px solid #ccc;">{{$product->hsn_code}}</td>
            <td style="border-bottom:1px solid #ccc;">{{number_format($product->mrp, 2)}}</td>
            <td style="border-bottom:1px solid #ccc;">{{number_format($singleUnitPrice, 2)}}</td>
            <td style="border-bottom:1px solid #ccc;">{{(int)$product->invoicedQty}}</td>
            <td style="border-bottom:1px solid #ccc;">{{number_format($product->invCfc, 2)}}</td>
            <td style="border-bottom:1px solid #ccc;">{{(float)$taxPer}}</td>
            <td style="border-bottom:1px solid #ccc;" align="right">{{number_format($taxValue, 2)}}</td>
            <td style="border-bottom:1px solid #ccc;" align="right">{{number_format($netValue, 2)}}</td>
            <td style="border-bottom:1px solid #ccc;" align="right">{{number_format($discount, 2)}}</td>
            <td style="border-bottom:1px solid #ccc;" align="right">{{number_format($subTotal, 2)}}</td>
            <?php
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
        
        <tr style="font-weight:bold;height:30px;">
         <td>&nbsp; </td>
          <td>&nbsp;</td>
          <td align="right">Total:</td>
          <td>&nbsp;</td>
          <td>{{$InvoicedQty}}</td>
          <td></td>
          <td></td>
          <td align="right">{{number_format($total_tax, 2)}}</td>
          <td align="right">{{number_format($total_net, 2)}}</td>
          <td align="right">{{number_format($total_discount, 2)}}</td>
          <td align="right">{{number_format($sub_total, 2)}}</td>
        </tr>
<?php //print_r($finalTaxArr); 

if($totSGST > 0 || $totCGST > 0 || $totIGST > 0) {
  $gstData = array('SGST'=>$totSGST, 'CGST'=>$totCGST, 'IGST'=>$totIGST, 'UTGST'=>$totUTGST);
}
else {
  $gstData = array();
}

$finalNewTaxArr = array();
foreach ($finalTaxArr as $key => $taxArr) {
  $finalNewTaxArr[$key] = array();
  $totAmt = 0;
  foreach ($taxArr as $tax) {
    $totAmt = $totAmt + $tax['taxamt'];
    $finalNewTaxArr[$key]['name'] = $tax['name'];
    $finalNewTaxArr[$key]['tax'] = $tax['tax'];
  }

  $finalNewTaxArr[$key]['tax_value'] = $totAmt;
}
?> 
@endif        
      </table>


<table cellpadding="3" cellspacing="0" width="100%" style="margin-top:10px; border:1px solid #ccc; font-size:11px;">
          <tr style="font-weight:bold;">
            <td height="30" bgcolor="#e7ecf1">Tot. Inv. Qty</td>
            <td bgcolor="#e7ecf1">Sub Total</td>
            <td bgcolor="#e7ecf1">Shipping Amt.</td>
            <td bgcolor="#e7ecf1">Total Sch. Disc.</td>
            <td bgcolor="#e7ecf1">Other Disc.</td>
            <td bgcolor="#e7ecf1">Total Disc.</td>
           @if(isset($finalNewTaxArr) && is_array($finalNewTaxArr) && count($gstData) <= 0)                                     
            @foreach($finalNewTaxArr as $tax)
            <td bgcolor="#e7ecf1">{{$tax['name']}} ({{isset($tax['tax']) ? (float)$tax['tax'] : 0}}%)</td>
            @endforeach
            @endif

            @if(is_array($gstData) && count($gstData) > 0)
            @foreach($gstData as $gstKey=>$gstVal)
            <td bgcolor="#e7ecf1">{{$gstKey}}</td>
            @endforeach
            @endif

            <td bgcolor="#e7ecf1">Total Tax</td>
            <td bgcolor="#e7ecf1">Roundoff</td>
            <td bgcolor="#e7ecf1">Grand Total</td>            
          </tr>
        

          <tr>
            <td height="30">{{$InvoicedQty}}</td>
            <td>{{$orderDetails->symbol}} {{ number_format($sub_total, 2) }}</td>
            <td>{{$orderDetails->symbol}} 0.00</td>
            <td>{{$orderDetails->symbol}} {{number_format($total_discount, 2)}}</td>
            <td>{{$orderDetails->symbol}} {{number_format($orderDetails->discount, 2)}}</td>
            <td>{{$orderDetails->symbol}} {{number_format(($total_discount + $orderDetails->discount), 2)}}</td>

          <?php //echo '<pre>';print_r($taxBreakup);print_r($finalNewTaxArr); ?>
          @if(isset($finalNewTaxArr) && is_array($finalNewTaxArr) && count($gstData) <= 0)                                     
          @foreach($finalNewTaxArr as $tax)
          <td>{{$orderDetails->symbol}} {{number_format((isset($tax['tax_value']) ? ($tax['tax_value']) : 0), 2)}}</td>
          @endforeach
          @endif

          @if(is_array($gstData) && count($gstData) > 0)
            @foreach($gstData as $gstKey=>$gstVal)
            <td>{{$orderDetails->symbol}} {{number_format($gstVal,2)}}</td>
            @endforeach
          @endif

            <td>{{$orderDetails->symbol}} {{number_format($total_tax, 2)}}</td>
            <?php 
            $grandTotal = $sub_total;
            $grandTotalWithRound = Utility::getRoundOff($grandTotal, 'gtround'); 
            $roundoff = Utility::getRoundOff($grandTotal, 'roundoff');  
            ?>
            <td>{{$orderDetails->symbol}} {{number_format($roundoff, 2)}}</td>
            <td>{{$orderDetails->symbol}} {{number_format($grandTotalWithRound, 2)}}</td>            
          </tr>
      </table>
@if(is_object($trackInfo))
<br>
<table cellpadding="1" cellspacing="1" class="table table-striped table-bordered table-advance table-hover" style=" word-wrap:break-word;font-size:13px;width:100%;">
@if($trackInfo->cfc_cnt!=0)<tr>
<td style="width:15%;">CFC</td><td style="width:10%;">{{(int)$trackInfo->cfc_cnt}}</td><td>{{isset($cratesList[16004]) ? $cratesList[16004] : ''}}</td>
</tr>@endif
@if($trackInfo->bags_cnt!=0)<tr>
<td style="width:15%;">Bags</td><td style="width:10%;">{{(int)$trackInfo->bags_cnt}}</td><td>{{isset($cratesList[16006]) ? $cratesList[16006] : ''}}</td>
</tr>@endif
@if($trackInfo->crates_cnt!=0)<tr>
<td style="width:15%;">Crates</td><td style="width:10%;">{{(int)$trackInfo->crates_cnt}}</td><td>
{{isset($cratesList[16007]) ? $cratesList[16007] : ''}}
</td>
</tr>@endif
</table>
@endif