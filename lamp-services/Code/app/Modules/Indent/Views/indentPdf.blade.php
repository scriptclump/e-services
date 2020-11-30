<style>
body {
	margin: 0px;
	padding: 0px;
	color: #333;
	font-family: "Open Sans",sans-serif !important;
}

.notific{font-size: 11px;}

</style>
@if(is_object($leInfo))
<table width="100%" border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td width="50%" align="left" valign="middle"><img src="{{url('/')}}/img/ebutor.png" alt="" height="42" width="42" style="float:left"> <strong style="float:left; line-height:42px;"> {{$leInfo->business_legal_name}}</strong></td>
    <td width="50%" align="right" valign="middle">
      <div style="padding-left:20px; padding-top:10px; font-size:12px !important; float:right;"> {{$leInfo->address1}}, {{$leInfo->address2}},<br>
        {{$leInfo->city}}, {{$leInfo->state_name}}, {{$leInfo->country_name}}, {{$leInfo->pincode}}.<br>
        <!-- <strong>Email :</strong> {{$companyInfo->email_id}} | <strong>Phone :</strong> {{$companyInfo->mobile_no}} --> </div>
    </td>
  </tr>
</table>
@endif
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center"><h4>INDENT</h4></td>
    </tr>
</table>

  <div class="row">
      <div class="col-md-12 text-right">
          <p class="notific">* <b>All Amounts in</b> <i class="fa fa-inr"></i></p>
      </div>    
  </div>


<table width="100%" style=" font-size:11px !important; border-collapse:collapse; border-right:1px solid #000;" cellspacing="0" cellpadding="0" border="1">
<tr>
    <td width="33%" align="left" valign="top" >
         <table width="100%" border="0" cellspacing="0" cellpadding="8" style="font-size:11px;">
    <tr bgcolor="#efefef">
        <td align="left" style=" border-bottom:1px solid #000; padding:10px 8px; font-size:14px !important;"><strong>Supplier</strong></td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Name:</strong> {{$supplier->business_legal_name}}</td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Address:</strong>  {{$supplier->address1}} 
          @if(!empty($supplier->address2)) 
          , {{$supplier->address2}}
          @endif
          <br>
          {{$supplier->city}}, {{$supplier->state_name}}<br>
          {{$supplier->country_name}}, {{$supplier->pincode}}</td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Phone:</strong> {{$supContact->mobile_no}}</td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Email:</strong> {{$supContact->email_id}}</td>
    </tr>
    </table>
  </td>
  <td width="33%" align="center" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="8" style="font-size:11px;">
    <tr bgcolor="#efefef">
      <td style=" border-bottom:1px solid #000;  padding:10px 8px;font-size:14px !important;"><strong>Delivery Address</strong></td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Name:</strong> {{$warehouse->lp_wh_name}}</td>
    </tr>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Address:</strong>  {{$warehouse->address1}}
        @if(!empty($warehouse->address2)) 
        , {{$warehouse->address2}}
        @endif
        <br>{{$warehouse->city}},{{$warehouse->state_name}}, {{$warehouse->country_name}}, {{$warehouse->pincode}}</td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Phone:</strong>  {{$warehouse->phone_no}}</td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Email:</strong>  {{$warehouse->email}}</td>
    </tr>
    </table>
  </td>
  <td width="33%" align="center" valign="top" >
    <table width="100%" border="0" cellspacing="0" cellpadding="8" style="font-size:11px;">
     <tr bgcolor="#efefef">
      <td style=" border-bottom:1px solid #000;  padding:10px 8px;font-size:14px !important;"><strong>Indent Details</strong></td>
      </tr>
    <tr>
      <td align="left" valign="top"><strong>Indent ID:</strong> {{$indentArr[0]->indent_code}}</td>
    </tr>
    <tr>
      <td align="left" valign="top"><strong>Indent Date:</strong> {{date('d-m-Y', strtotime($indentArr[0]->indent_date))}}</td>
    </tr>
    <tr>
      <td align="left" valign="top"><strong>Indent Type:</strong> {{($indentArr[0]->indent_type == 1 ? 'Manual' : 'Auto')}}</td>
    </tr>
    </table>
  </td>
</tr>
</table>
<br>
<strong style="font-size:16px !important; ">Product Description</strong>
<table width="100%" style=" font-size:11px !important; border-collapse:collapse; border-right:1px solid #000; margin-top:10px" cellspacing="0" cellpadding="10" border="1">
<thead>
<tr bgcolor="#efefef" style="font-size:12px !important; font-weight:bold;">
  <td  width="10%" height="25" align="center">S No</td>
  <td  width="15%" align="center">SKU</td>
  <!-- <td  width="15%" align="center">EAN</td> -->
  <td  width="30%">Product Name</td>  
  <td  width="15%" align="center">MRP</td>    
  <td  width="15%" align="center">Indent Qty&nbsp;(CFC)</td>
  <td  width="15%" align="center">CFC LP</td>
</tr>
</thead>
<?php 
$slno = 1;
$sumOfIndentQty = 0; 
?>
@foreach($indentArr as $product)
<tr>
  <td height="25"  align="center">{{$slno}}</td>
  <td  align="center">{{$product->sku}}</td>
  <!-- <td  align="center">{{(isset($product->upc) ? $product->upc : $product->seller_sku)}}</td> -->
  <td >{{$product->pname}}</td>  
  <td  align="center">{{number_format($product->mrp, 2)}}</td>
  <td>{{(int)$product->qty}}<span style="margin-left: 5px; margin-top:5px;">(@if($product->prod_eaches!='') {{$product->prod_eaches}} @else {{0}} @endif Eaches)</span></td>
  <td>{{number_format($product->max_elp,2)}}</td>
</tr>
<?php 
$slno = ($slno +1);
$sumOfIndentQty = ($sumOfIndentQty + $product->qty);
?>
@endforeach
<tr>
<!-- <td height="25"  colspan="4"></td> -->
<td  align="right" colspan="4">Total</td>
<td  align="center">{{(int)$sumOfIndentQty}}</td>
<td>&nbsp;</td>
</tr>
</table>