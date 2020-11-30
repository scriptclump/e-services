<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Picklist</title>
<style type="text/css">
.container{width:1000px; margin:0 auto;}
@media print {
body {-webkit-print-color-adjust: exact;}
thead {display: table-header-group;}
}
td{ font-size: 14px;}
.bborder{border-bottom:1px solid #000;border-right:1px solid #000;}
/* onload="window.print();"*/
</style>
</head>

<body onload="window.print();">
<div class="container">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
  <td width="50%" align="left" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="left" valign="middle"><img src="/img/ebutor.png" alt="" height="42" width="42" style="float:left;"><h2 style="float:left; padding-left:10px; padding-top:6px; font-size:18px; font-family:arial, sans-serif;">{{$leDetail->business_legal_name}}</h2>
        </td>
        </tr>
        <tr>
    	    <td align="left" valign="middle">
          <span style="font-size:12px; font-family:arial, sans-serif;"><strong>
    {{$leDetail->address1}}, @if(!empty($leDetail->address2)){{$leDetail->address2}},<br>@endif
    {{$leDetail->city}}, {{$leDetail->state_name}}, {{isset($leDetail->country_name) ? $leDetail->country_name : 'India'}}, {{$leDetail->pincode}}</strong></span></td>
        </tr>
    </table>
  </td>
  <td width="50%" align="left" valign="top" style="float:right;">
      <table width="100%" align="right" cellpadding="0" cellspacing="7" style="float:right; text-align:left; font-family:arial, sans-serif;font-size:12px; font-weight:normal;margin-top:12px">
        <tr>
        <th>Date</th>
        <th>:</th>
        <td>{{date('d-m-Y')}}</td>
        </tr>
        <tr>
        <th>DC</th>
        <th>:</th>
        <td>{{$DC}}</td>
        </tr>
        <tr>
        <th>Picked By</th>
        <th>:</th>
        <td>{{$pickerName}}</td>
        </tr>
        <tr>
        <th>Checked By</th>
        <th>:</th>
        <td></td>
        </tr>
      </table>

  </td>
</tr>
</table>
<h1 style="border-bottom:0px solid #323334; font-family: arial, sans-serif; text-align:center;padding-bottom:15px; font-size:16px;">PICKLIST</h1>
<table border="0" width="100%" cellpadding="2" cellspacing="0" style="font-family: arial, sans-serif; font-size:12px; text-align:left;">
<thead>
<tr style="font-size:12px; font-weight:silver;">
  <th width="3%" class="bborder" style="border-top:1px solid #000;border-left:1px solid #000;">SNo.</th>
  <th width="8%" class="bborder" style="border-top:1px solid #000;">Bin Location</th>
  <th width="5%" class="bborder" style="border-top:1px solid #000; font-size:12px;">SO (ESU)</th>
  <th width="10%" class="bborder" style="border-top:1px solid #000;">SKU</th>
  <th width="35%" class="bborder" style="border-top:1px solid #000;">Product Description</th>
  <th width="5%" class="bborder" style="border-top:1px solid #000;">EAN</th>
  <th width="6%" class="bborder" style="border-top:1px solid #000;" align="right">MRP</th>
  <th width="5%" class="bborder" style="border-top:1px solid #000; font-size:12px;">SO (EA)</th>
  <th width="5%" class="bborder" style="border-top:1px solid #000;">Pick QTY</th>
  <th width="15%" class="bborder" style="border-top:1px solid #000;">Remarks</th>
  </tr>
  </thead>
<?php
$sno = 1;
?>

	@foreach($orderProducts as $orderProduct)
  <tr style="background-color:#cccccc">
    <td class="bborder" style="border-left:1px solid #000;">{{$sno}}</td>
    <td class="bborder" colspan="2"><strong>ID:</strong> {{isset($orderProduct[0]->order_code) ? $orderProduct[0]->order_code : $orderProduct[0]->gds_order_id}}</td>
    <td class="bborder" colspan="2"><strong>Area:</strong> {{(isset($orderProduct[0]->area) ? $orderProduct[0]->area : '')}} &nbsp;<strong>Shop:</strong> {{$orderProduct[0]->shop_name}}</td>
    <td class="bborder" colspan="3"><strong>M:</strong> {{$orderProduct[0]->phone_no}}</td>
    <td class="bborder" colspan="2"><strong>Sch. Date :</strong> {{$orderProduct[0]->scheduled_delivery_date}}</td>
  </tr>
	
	@foreach($orderProduct as $singleProduct)
  <?php $orderedQty = ($singleProduct->ordered_qty - $singleProduct->canceled_qty); ?>
  <tr>
    <td class="bborder" style="border-left:1px solid #000;">&nbsp;</td>
    <td class="bborder">&nbsp;</td>
    <td class="bborder" align="right">{{(int)$singleProduct->esu_qty}}</td>
    <td class="bborder">{{$singleProduct->article_no}}</td>
    <td class="bborder">{{$singleProduct->pname}}</td>
    <td class="bborder">{{$singleProduct->upc}}</td>
    <td class="bborder" align="right"><strong>{{number_format($singleProduct->mrp, 1)}}</strong></td>
    <td class="bborder" align="right">{{(int)$orderedQty}}</td>
    <td class="bborder"></td>
    <td class="bborder">{{($singleProduct->base_price==0) ? 'Freebie' : ''}}</td>
  </tr>
  	@endforeach
	<?php $sno++ ?>
  	@endforeach
</table>
</div>
</body>
</html>
