<html dir="ltr" lang="en">
<head>
<meta charset="UTF-8">
<title>TAX INVOICE</title>


<style>
@media print {body {-webkit-print-color-adjust: exact;}}
body {
  margin: 0px;
  padding: 0px;
  color: #333;
  font-family: "Open Sans", sans-serif !important;
  -webkit-print-color-adjust: exact;
}

table {
    border-collapse: collapse;
}
.hedding1{
   background-color: #c0c0c0 !important;
   color: #000 !important;   
   -webkit-print-color-adjust: exact !important;
   
}
.table-bordered, .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td{padding:4px;}
.table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th{padding:5px;}
.table-striped>tbody>tr:nth-of-type(odd) {
    background-color: #fbfcfd !important;
  -webkit-print-color-adjust: exact !important;
}
.printmartop {margin-top: 10px;}
.container {margin-top: 20px;}

.small1 {font-size: 73%;}
.small2 {font-size: 65.5%;}
.bg {background-color: #efefef;padding: 8px 0px;}
.bold{font-weight: bold;}


.table-bordered>tbody>tr>td{border: 1px solid #000 !important;}
.table-bordered>thead>tr>th{border: 1px solid #000 !important;}

.page-break{ display: block !important; clear: both !important; page-break-after:always !important;}

.table-headings th{background:#c0c0c0 !important; font-weight:bold !important; border:1px solid #000 !important;}

</style>

</head>
<body>
<div class="container">
<div class="row">
<div class="col-md-12">
<table width="100%" border="0" cellspacing="5" cellpadding="5">
<tr>
<td width="50%" align="left" valign="top">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      @if(is_object($leInfo) and $leInfo->logo != "" and $leInfo->logo != null)
        <img src="{{$leInfo->logo}}" alt="Image" height="42" width="42" >
      @endif
    </td>
    <td><strong style="padding-top:-20px;">@if(is_object($leInfo)){{$leInfo->business_legal_name}}@endif</strong></td>
  </tr>
</table>

 

</td>
<td width="50%" align="right" valign="middle"><div style="padding-left:20px; padding-top:10px; font-size:12px; float:right;">
@if(is_object($lewhInfo))
{{$lewhInfo->address1}} {{(empty($lewhInfo->address2) ? '' : ','.$lewhInfo->address2.',')}}<br>
{{$lewhInfo->city}}, {{$lewhInfo->state_name}}, {{(empty($lewhInfo->country_name) ? 'India' : $lewhInfo->country_name)}}, {{$lewhInfo->pincode}},{{$lewhInfo->fssai}}
</br>TIN No : {{$lewhInfo->tin_number}}</div>@endif</td>
</tr>
</table>
</div>
</div>
<div class="row" style="margin:5px 0px;">
<div class="col-md-12 text-center">
<h4 style="text-align:center">TAX INVOICE</h4>
</div>
</div>

<div class="row">
<div class="col-md-12">
<table width="100%" class="table table-bordered thline printtable " cellpadding="1">
<tr style="font-size:16px; text-align:left" class="hedding1 table-headings">
<th width="33%" >Customer</th>
<th width="33%" >Shipping Address</th>
<th width="33%" >Invoice Details</th>
</tr>
<tr>
<td valign="top" style="font-size:14px;">
<strong>Name:</strong> {{$orderDetails->firstname}} {{$orderDetails->lastname}} <br>
<strong>Billing Address</strong><br>
@if(is_object($billing))   
{{$billing->addr1}} {{$billing->addr2}},<br>
@if(!empty($billing->locality)) {{$billing->locality}}, @endif @if(!empty($billing->landmark)){{$billing->landmark}}, @endif {{$billing->city}}, {{$billing->state_name}}, {{$billing->country_name}}, {{$billing->postcode}},{{$billing->fssai
}}<br>
<strong>Phone:</strong> {{$orderDetails->phone_no}} <br>
@if(!empty($orderDetails->beat))<strong>Beat:</strong> {{$orderDetails->beat}}<br>@endif
@if(!empty($orderDetails->areaname))<strong>Area:</strong> {{$orderDetails->areaname}}<br>@endif
@endif                  
</td>
<td valign="top" style="font-size:14px;">
@if(is_object($shipping))
<strong>{{ucwords($orderDetails->shop_name)}}</strong><br>  
{{$shipping->fname}} {{$shipping->mname}} {{$shipping->lname}}<br>
{{$shipping->addr1}} {{$shipping->addr2}},<br>
@if(!empty($shipping->locality)) {{$shipping->locality}}, @endif @if(!empty($shipping->landmark)){{$shipping->landmark}}, @endif {{$shipping->city}}, {{$shipping->state_name}}, {{$shipping->country_name}}, {{$shipping->postcode}},{{$shipping->fssai}}<br>
<strong>Telephone:</strong> {{$shipping->telephone}} 
@if(!empty($shipping->mobile))<strong>Mobile:</strong> {{$shipping->mobile}} @endif
@endif 

</td>
<td valign="top" style="font-size:14px;">
<!--<strong>Invoice No.:</strong> {{isset($products[0]->invoice_code) ? $products[0]->invoice_code : $products[0]->gds_invoice_grid_id}}<br>
<strong>Invoice Date:</strong> {{date('d-m-Y h:i A', strtotime($products[0]->invoice_date))}}<br>
<strong>SO No. / Date:</strong> {{$orderDetails->order_code}} / {{date('d-m-Y h:i A', strtotime($orderDetails->order_date))}}<br>-->
<strong>SO No. / Date:</strong> {{$orderDetails->order_code}} / {{date('d-m-Y h:i A', strtotime($orderDetails->order_date))}}<br>
@if(!empty($lewhInfo->le_wh_code)) <strong>DC No:</strong> {{$lewhInfo->le_wh_code}}<br> @endif
<strong>DC Name:</strong> {{$lewhInfo->lp_wh_name}}<br>
@if(!empty($orderDetails->hub_name)) <strong>Hub Name:</strong> {{$orderDetails->hub_name}}<br> @endif
<strong>Jurisdiction Only</strong> : Hyderabad
@if(isset($userInfo->firstname) && isset($userInfo->lastname))
<br><strong>Created By</strong>: {{$userInfo->firstname}} {{$userInfo->lastname}} (M: {{isset($userInfo->mobile_no) ? $userInfo->mobile_no : ''}})
@endif

@if(isset($pickerInfo->firstname) && isset($pickerInfo->lastname))
<br><strong>Picked By </strong>: {{$pickerInfo->firstname}} {{$pickerInfo->lastname}}
@endif

@if(isset($delSlots[$orderDetails->pref_slab1]) && $delSlots[$orderDetails->pref_slab1]!='')
<br><strong>Del Slot1</strong>: {{$delSlots[$orderDetails->pref_slab1]}}
@endif
@if(isset($delSlots[$orderDetails->pref_slab2]) && $delSlots[$orderDetails->pref_slab2]!='')
<br><strong>Del Slot2</strong>: {{$delSlots[$orderDetails->pref_slab2]}}
@endif
<br><strong>Sch Delivery Date</strong>: {{date('d-m-Y',strtotime($orderDetails->scheduled_delivery_date))}}
</td>
</tr>
</table>
</div>
</div>
  <br>
<div class="row">
<div class="col-md-12">
<table cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-advance table-hover" style=" word-wrap:break-word; font-size:13px;">
  <thead>
<tr class="hedding1 table-headings" >
<th align="left">SNO</th>
<th align="left">SKU</th>
<th align="left">Product Name</th>
<th >MRP(Rs.)</th>
<th >Unit(Rs.)</th>
<th >Inv. Qty</th>
<th >Inv. CFC</th>
<th >Net(Rs.)</th>
<th >Tax %</th>
<th >Tax(Rs.)</th>
<th >Sch. Disc.</th>
<th >Total(Rs.)</th>
</tr>
</thead>
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

$sno = 1; 
$tax = 0;
$discount = 0;
$shippingAmount = 0;
$otherDiscount = 0;
$grandTotal = 0;
$totInvoicedQty = 0;
$finalTaxArr = array();
?>
@foreach($products as $product)
<tr>
<?php
//print_r($product);            
$taxName = (isset($prodTaxes[$product->product_id]['name']) ? $prodTaxes[$product->product_id]['name'] : 0);
$taxPer = (isset($prodTaxes[$product->product_id]['tax']) ? $prodTaxes[$product->product_id]['tax'] : 0);
$tax_value =  (isset($prodTaxes[$product->product_id]['tax_value']) ? $prodTaxes[$product->product_id]['tax_value'] : 0);


$singleUnitPrice = (($product->total / (100+$taxPer)*100) / $product->qty);


//$singleUnitPrice = ($product->single_price / (int)$product->qty);

$unitPrice = ($singleUnitPrice * $product->invoicedQty);
$taxValue = (($singleUnitPrice * $taxPer) / 100 ) * $product->invoicedQty;
$netValue = ($singleUnitPrice * $product->invoicedQty);
$subTotal = $taxValue + $netValue;
$discount = 0;
$taxkey = $taxName.'-'.$taxPer;
if($taxkey != '0-0') {
  $finalTaxArr[$taxkey][] = array('tax'=>$taxPer, 'name'=>$taxName, 'qty'=>$product->qty, 'tax_value'=>$tax_value, 'taxamtPer'=>($tax_value/$product->qty), 'taxamt'=>(($tax_value/$product->qty)*$product->invoicedQty));
}

?>
<td>{{$sno}}</td>
<td>{{$product->sku}}</td>
<td >{{$product->pname}} {{!empty($product->seller_sku) ? '('.$product->seller_sku.')' : ''}}</td>
<td align="right">{{number_format($product->mrp, 2)}}</td>
<td align="right">{{number_format($singleUnitPrice, 2)}}</td>
<td align="center">{{(int)$product->invoicedQty}}</td>
<td align="center">{{number_format($product->invCfc, 2)}}</td>
<td align="right">{{number_format($netValue, 2)}}</td>
<td align="right">{{(float)$taxPer}}</td>
<td align="right">{{number_format($taxValue, 2)}}</td>
<td align="right">{{number_format($discount, 2)}}</td>
<td align="right">{{number_format($subTotal, 2)}}</td>
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
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp; </td>
<td>&nbsp;</td>
<td align="right"><strong>Total:</strong></td>
<td align="center"><strong>{{$InvoicedQty}}</strong></td>
<td align="center"><strong></strong></td>
<td align="right"><strong>{{number_format($total_net, 2)}}</strong></td>
<td align="right"></td>
<td align="right"><strong>{{number_format($total_tax, 2)}}</strong></td>
<td align="right"><strong>{{number_format($total_discount, 2)}}</strong></td>
<td align="right"><strong>{{number_format($sub_total, 2)}}</strong></td>
</tr>
<?php //print_r($finalTaxArr); 

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
</table>
</div>
</div>
  <br>
<div class="row">
<div class="col-md-12">
<table cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-advance table-hover" style=" word-wrap:break-word;font-size:13px;">

<tr class="hedding1 table-headings">
<th align="left">Tot. Inv. Qty</th>
<th align="left">Sub Total</th>
<th align="left">Shipping Amt.</th>
<th align="left">Total Sch. Disc.</th>
<th align="left">Other Disc.</th>
<th align="left">Total Disc.</th>
@if(isset($finalNewTaxArr) && is_array($finalNewTaxArr))                                     
@foreach($finalNewTaxArr as $tax)
<th align="left">{{$tax['name']}} ({{isset($tax['tax']) ? (float)$tax['tax'] : 0}}%)</th>
@endforeach
@endif
<th align="left">Total Tax</th>
<th align="left">Roundoff</th>
<th align="left">Grand Total</th>
</tr>


<tr class="odd gradeX">
<td>{{$InvoicedQty}}</td>
<td>{{$orderDetails->symbol}} {{ number_format($sub_total, 2) }}</td>
<td>{{$orderDetails->symbol}} 0.00</td>
<td>{{$orderDetails->symbol}} {{number_format($total_discount, 2)}}</td>
<td>{{$orderDetails->symbol}} {{number_format($orderDetails->discount, 2)}}</td>
<td>{{$orderDetails->symbol}} {{number_format(($total_discount + $orderDetails->discount), 2)}}</td>
<?php //echo '<pre>';print_r($taxBreakup);print_r($finalNewTaxArr); ?>
@if(isset($finalNewTaxArr) && is_array($finalNewTaxArr))                                     
@foreach($finalNewTaxArr as $tax)
<td>{{$orderDetails->symbol}} {{number_format((isset($tax['tax_value']) ? ($tax['tax_value']) : 0), 2)}}</td>
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
<table cellpadding="1" cellspacing="1" class="table table-striped table-bordered table-advance table-hover" style=" word-wrap:break-word;font-size:13px;width:80%;">
@if($trackInfo->cfc_cnt!=0)<tr>
<td style="width:15%;">CFC</td><td style="width:10%;">{{(int)$trackInfo->cfc_cnt}}</td><td>{{isset($cratesList[16004]) ? $cratesList[16004] : ''}}</td>
</tr>@endif
@if($trackInfo->bags_cnt!=0)<tr>
<td style="width:15%;">Bags</td><td style="width:10%;">{{(int)$trackInfo->bags_cnt}}</td><td>{{isset($cratesList[16006]) ? $cratesList[16006] : ''}}</td>
</tr>@endif
@if($trackInfo->crates_cnt!=0)<tr>
<td style="width:15%;">Crates</td><td style="width:10%;">{{(int)$trackInfo->crates_cnt}}</td><td>
{{isset($cratesList[16007]) ? $cratesList[16007] : ''}}</td>
</tr>@endif
</table>
@endif
</div>
</div>
  <div class="page-break"></div>
</div>


</body>
</html>
