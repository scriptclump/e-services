<html dir="ltr" lang="en">
<head>
<meta charset="UTF-8">
<title>DELIVERY CHALLAN</title>
</head>
<style>
body {
	margin: 0px;
	padding: 0px;
	color: #333;
	font-family: "Open Sans", sans-serif !important;
	-webkit-print-color-adjust: exact;
}
@media print {body {-webkit-print-color-adjust: exact;}}
.hedding1{
	 background-color: #c0c0c0 !important;
   color: #000 !important;   
   -webkit-print-color-adjust: exact !important; 
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{padding:4px !important;}
.table-striped>tbody>tr:nth-of-type(odd) {
    background-color: #fbfcfd !important;
	-webkit-print-color-adjust: exact !important;
}
.page-break { display: block; clear: both; page-break-before: always; }
</style>

<?php $curSymbol = isset($orderDetails->symbol) ? trim($orderDetails->symbol) : 'Rs.'; ?>
@if(is_object($leInfo))
<table width="100%" border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td width="50%" align="left" valign="middle">
        @if(is_object($leInfo) and $leInfo->logo != "" and $leInfo->logo != "null")
            <img src="{{$leInfo->logo}}" alt="" height="30" width="30" style="float:left">
        @endif
       <?php /*<img src="{{url('/')}}/img/ebutor.png" alt="" height="30" width="30" style="float:left"> */ ?>
        <strong style="float:left; line-height:32px; ">
            {{$leInfo->business_legal_name}}</strong></td>
    <td width="50%" align="right" valign="middle"><div style="padding-left:20px; padding-top:10px; font-size:10px; float:right;">{{$leInfo->address1}} {{(empty($leInfo->address2) ? '' : ','.$leInfo->address2.',')}}<br>
        {{$leInfo->city}}, {{isset($legalEntity->state_name) ? $legalEntity->state_name : ''}}, {{(empty($leInfo->country_name) ? 'India': $leInfo->country_name)}}, {{$leInfo->pincode}} </div></td>
  </tr>
</table>
@endif
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><h5 style="margin-bottom:6px;">DELIVERY CHALLAN</h5></td>
  </tr>
</table>
<table width="100%" style="font-size:11px; border-collapse:collapse; border-right:1px solid #000;" cellspacing="0" cellpadding="2" border="1">
  <tr style="font-size:12px; font-weight: bold; padding:5px;" height="30px" class="hedding1">
    <td width="33%" ><strong>Customer</strong></td>
    <td width="33%" ><strong>Shipping Address</strong></td>
    <td width="33%"><strong>Order Details</strong></td>
  </tr>
  <tr>
    <td valign="top" style="padding:5px;"><strong>Name:</strong> {{$orderDetails->firstname}} {{$orderDetails->lastname}} <br>
      <strong style="font-size:12px;">Billing Address</strong><br>
      @if(is_object($billing))
      {{$billing->addr1}} {{$billing->addr2}}, {{$billing->city}}, {{$billing->state_name}}, {{$billing->country_name}}, {{$billing->postcode}}<br>
      <strong>Phone:</strong> {{$orderDetails->phone_no}}
      @endif </td>
    <td valign="top" style="padding:5px;" >
    @if(is_object($shipping)) <strong>{{$orderDetails->shop_name}}</strong><br>
      <strong>Name:</strong>{{$shipping->fname}} {{$shipping->mname}} {{$shipping->lname}}<br>
      <strong>Address:</strong>{{$shipping->addr1}} {{$shipping->addr2}}<br>
      {{$shipping->city}}, {{$shipping->state_name}}, {{$shipping->country_name}}, {{$shipping->postcode}}<br>
      <strong>Telephone:</strong> {{$shipping->telephone}}<br>
      @if(!empty($shipping->mobile)) <strong>Mobile:</strong> {{$shipping->mobile}} @endif
      @endif 
      @if(!empty($shipping->locality))<strong>Locality:</strong> {{$shipping->locality}}@endif  <br>
      @if(!empty($shipping->landmark))<strong>Landmark:</strong> {{$shipping->landmark}}@endif  </td>
    <td valign="top" style="padding:5px;"><strong>Order ID:</strong> {{$orderDetails->order_code}}<br>
      <strong>Order Date:</strong> {{date('d-m-Y h:i A', strtotime($orderDetails->order_date))}}<br>
      @if(!empty($lewhInfo->le_wh_code))
      <strong>DC No:</strong> {{$lewhInfo->le_wh_code}}<br>
      @endif 
      @if(isset($lewhInfo->lp_wh_name))<strong>DC Name:</strong> {{(isset($lewhInfo->lp_wh_name) ? $lewhInfo->lp_wh_name : '')}}
      @endif

      @if(isset($userInfo->firstname) && isset($userInfo->lastname)) <br>
      <strong>Created By</strong>: {{(isset($userInfo->firstname) ? $userInfo->firstname : '')}} {{(isset($userInfo->lastname) ? $userInfo->lastname : '')}} (M: {{isset($userInfo->mobile_no) ? $userInfo->mobile_no : ''}})
      @endif </td>
  </tr>
</table>


<table width="100%" style="font-size:12px !important; border-collapse:collapse; border-right:1px solid #000; margin-top:10px;white-space: nowrap;" cellspacing="0" cellpadding="2" border="1">
  <thead>
  <tr  class="hedding1" bgcolor="#efefef" style="font-size:11px !important; font-weight:bold; padding:4px !important;">
    <td >SNO</td>
    <td >SKU</td>
    <td width="25%" >Product Name</td>
    <td  align="right">MRP(Rs.)</td>
    <td  align="right">Rate</td>
    <td  align="right">Qty</td>
    <td  align="right">Tax %</td>
    <td  align="right">Tax Amt(Rs.)</td>
    <td  align="right">Sch. Disc.</td>
    <td  align="right">Total(Rs.)</td>
  </tr>
</thead>
  @if(is_array($products) && count($products))
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
?>
  @foreach($products as $product)
  <?php
$taxPer = (isset($prodTaxes[$product->product_id]['tax']) ? $prodTaxes[$product->product_id]['tax'] : 0);

$taxValue = (isset($prodTaxes[$product->product_id]['tax_value']) ? $prodTaxes[$product->product_id]['tax_value'] : 0);
$netValue = (int)$product->qty*$product->price;
$discountValue = (int)($product->price*$discount)/100;
$totalValue = (int)(($netValue+$taxValue)-($discountValue));

$orderQty = $product->qty; 
$unitBasePrice = ((round($product->total,2)/(100+$taxPer))*100)/$orderQty;

?>
  <tr>
  <td align="center"><p>{{$sno}}</p></td>
    <td style="padding:0px 5px;">{{$product->sku}}</td>
    <td>{{$product->pname}}</td>
    <td  align="right">{{$product->mrp}}</td>
    <td  align="right">{{number_format($unitBasePrice, 2)}}</td>
    <td align="right">{{(int)$orderQty}}</td>
    <td align="right">{{(float)$taxPer}}</td>
    <td  align="right">{{number_format($taxValue, 2)}}</td>
    <td  align="right">{{number_format($discountValue, 2)}}</td>
    <td  align="right">{{number_format($product->total, 2)}}</td>
    <?php
	$sno = $sno + 1; 
$total_discount+=$discountValue;
$total_tax+=$taxValue;
$total_tax_value+=$tax;
$total_qty+=$product->qty;
$sub_total = $sub_total + $product->total;
?>
  </tr>
  @endforeach
  @endif
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right"><strong>Total:</strong></td>
    <td align="right"><strong>{{$total_qty}}</strong></td>
    <td></td>
    <td align="right"><strong>{{$orderDetails->symbol}} {{number_format($total_tax, 2)}}</strong></td>
    <td align="right"><strong>{{$orderDetails->symbol}} {{number_format($total_discount, 2)}}</strong></td>
    <td align="right"><strong>{{$orderDetails->symbol}} {{number_format($sub_total, 2)}}</strong></td>
  </tr>
</table>

 <?php
 if(is_array($products) && count($products)) {
 ?> 
<table width="100%" style=" font-size:11px !important; margin-top:10px; border-collapse:collapse; border-right:1px solid #000;" cellspacing="0" cellpadding="3" border="1">
  <tr class="hedding1" style="font-size:11px !important; font-weight:bold;">
    <td >Sub Total</td>
    <td >Shipping Amount</td>
    <td >Total Scheme Discount</td>
    <td >Other Discount</td>
    <td >Total Discount</td>
    @if(isset($taxBreakup) && is_array($taxBreakup))                                     
    @foreach($taxBreakup as $taxName=>$taxData)
    <td >{{$taxData['name']}} ({{isset($taxData['tax']) ? (float)$taxData['tax'] : 0}}%)</td>
    @endforeach
    @endif
    <td >Total Tax</td>
    <td >Grand Total</td>
  </tr>
  <tr>
    <td>{{$orderDetails->symbol}} {{ number_format($sub_total, 2) }}</td>
    <td>{{$orderDetails->symbol}} 0.00</td>
    <td>{{$orderDetails->symbol}} {{number_format($total_discount, 2)}}</td>
    <td>{{$orderDetails->symbol}} {{number_format($orderDetails->discount, 2)}}</td>
    <td>{{$orderDetails->symbol}} {{number_format(($total_discount + $orderDetails->discount), 2)}}</td>
    @if(isset($taxBreakup) && is_array($taxBreakup))                                     
    @foreach($taxBreakup as $taxName=>$taxData)
    <td>{{$orderDetails->symbol}} {{number_format((isset($taxData['tax_value']) ? $taxData['tax_value'] : 0), 2)}}</td>
    @endforeach
    @endif
    <?php
    $grandTotal = $sub_total;
    ?>
    <td>{{$orderDetails->symbol}} {{number_format($total_tax, 2)}}</td>
    <td>{{$orderDetails->symbol}} {{number_format($grandTotal, 2)}}</td>
  </tr>
</table>
<?php } ?>
</html>
