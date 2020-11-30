<style>
body {
    margin: 0px;
    padding: 0px;
    color: #333;
    font-family: "Open Sans",sans-serif !important;
}
</style>
<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>
<table width="100%" border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td width="100%" align="left" valign="middle"><img src="http://portal.ebutor.com/img/ebutor.png" alt="" height="42" width="42"  style="float:left" > <strong style="float:left; line-height:42px;"> {{$leDetail->business_legal_name}}</strong></td>
    <td width="50%" align="right" valign="middle">      
    </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center"><h4>Purchase Order</h4></td>
    </tr>
</table>

<table width="100%" style=" font-size:11px !important; border-collapse:collapse; border-right:1px solid #000;" cellspacing="0" cellpadding="0" border="1">
<tr>
    <td width="25%" align="left" valign="top">
        <table width="100%" border="0" cellspacing="0" cellpadding="4" style="font-size:11px;">
    <tr bgcolor="#efefef">
        <td align="left" style=" border-bottom:1px solid #000; padding:10px 8px; font-size:14px !important;"><strong>Supplier</strong></td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Name:</strong> {{$supplier->business_legal_name}}</td>      
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Address:</strong> {{$supplier->address1}}, 
          @if(!empty($supplier->address2)) 
          <br/>{{$supplier->address2}}
          @endif
          <br>
          {{$supplier->city}}, {{$supplier->state_name}}, {{$supplier->country_name}}, {{$supplier->pincode}}</td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Phone:</strong> {{(isset($userInfo->mobile_no) ? $userInfo->mobile_no : '')}}</td>
     </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Email:</strong> {{(isset($userInfo->email_id) ? $userInfo->email_id : '')}}</td>
    </tr>
    @if(!empty($supplier->sup_bank_name))
    <tr>
      <td style="padding-left:10px;"><strong>Bank Name:</strong> {{(!empty($supplier->sup_bank_name) ? $supplier->sup_bank_name : 'NA')}}</td>
    </tr>
    @endif
    @if(!empty($supplier->sup_account_no))
    <tr>
      <td style="padding-left:10px;"><strong>A/c No:</strong> {{(!empty($supplier->sup_account_no) ? $supplier->sup_account_no : 'NA')}}</td>
    </tr>
    @endif
    @if(!empty($supplier->sup_account_name))
    <tr>
      <td style="padding-left:10px;"><strong>A/c Name.:</strong> {{(!empty($supplier->sup_account_name) ? $supplier->sup_account_name : 'NA')}}</td>
    </tr>
    @endif
    @if(!empty($supplier->sup_ifsc_code))
    <tr>
      <td style="padding-left:10px;"><strong>IFSC Code:</strong> {{(!empty($supplier->sup_ifsc_code) ? $supplier->sup_ifsc_code : 'NA')}}</td>
    </tr>
    @endif
    @if(isset($supplier->state_name) && !empty($supplier->state_name))
    <tr>
      <td style="padding-left:10px;"><strong>State:</strong> {{$supplier->state_name}}</td>
    </tr>
    @endif
    @if(isset($supplier->state_code) && !empty($supplier->state_code))
    <tr>
      <td style="padding-left:10px;"><strong>State Code:</strong> {{$supplier->state_code}}</td>
    </tr>
    @endif
    @if(isset($supplier->pan_number) && !empty($supplier->pan_number))
    <tr>
      <td style="padding-left:10px;">
        <strong>PAN:</strong> {{$supplier->pan_number}}
       </td>
    </tr>
    @endif
    @if(isset($supplier->gstin) && !empty($supplier->gstin))
    <tr>
      <td style="padding-left:10px;">
        <strong>GSTIN / UIN:</strong> {{$supplier->gstin}}
        </td>
    </tr>
    @endif
    @if(isset($supplier->fssai) && !empty($supplier->fssai))
    <tr>
      <td style="padding-left:10px;">
        <strong>FSSAI NO:</strong> {{$supplier->fssai}}
        </td>
    </tr>
    @endif     
    </table>
  </td>

  <td width="25%" align="center" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="4" style="font-size:11px;">
    <tr bgcolor="#efefef">
      <td style=" border-bottom:1px solid #000;  padding:10px 8px;font-size:14px !important;"><strong>Shipping Address</strong></td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Name:</strong> {{$whDetail->lp_wh_name}}</td>
    </tr>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Address:</strong> {{$whDetail->address1}},
        @if(!empty($whDetail->address2)) 
        </br> {{$whDetail->address2}}
        @endif
        <br>{{$whDetail->city}},{{$whDetail->state_name}}, {{$whDetail->country_name}}, {{$whDetail->pincode}}</td>
    </tr>
    @if(!empty($whDetail->contact_name))
    <tr>
      <td style="padding-left:10px;"><strong>Contact Person:</strong> {{$whDetail->contact_name}}</td>
    </tr>
    @endif
    <tr>
      <td style="padding-left:10px;"><strong>Phone:</strong> {{$whDetail->phone_no}}</td>      
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Email:</strong> {{$whDetail->email}}</td>
    </tr>
    @if(isset($whDetail->state_name) && !empty($whDetail->state_name))
    <tr>
      <td style="padding-left:10px;"><strong>State:</strong> {{$whDetail->state_name}}</td>
    </tr>
    @endif
    @if(isset($whDetail->state_code) && !empty($whDetail->state_code))
    <tr>
      <td style="padding-left:10px;"><strong>State Code:</strong> {{$whDetail->state_code}}</td>
    </tr>
    @endif
    </table>
  </td>
  <td width="20%" align="center" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="4" style="font-size:11px;">
    <tr bgcolor="#efefef">
      <td style=" border-bottom:1px solid #000;  padding:10px 8px;font-size:14px !important;"><strong>Billing Address</strong></td>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Name:</strong> {{$whDetail->business_legal_name}}</td>
    </tr>
    </tr>
    <tr>
      <td style="padding-left:10px;"><strong>Address:</strong> {{$whDetail->address1}},
          <?php if ($whDetail->address2 != "") { ?></br> {{$whDetail->address2}},<?php } ?>
        <br>{{$whDetail->city}},{{$whDetail->state_name}}, {{(empty($whDetail->country_name) ? 'India' : $whDetail->country_name)}} - {{$whDetail->pincode}}
      </td>
    </tr>
    @if(isset($whDetail->state_name) && !empty($whDetail->state_name))
    <tr>
      <td style="padding-left:10px;"><strong>State:</strong> {{$whDetail->state_name}}</td>
    </tr>
    @endif
    @if(isset($whDetail->state_code) && !empty($whDetail->state_code))
    <tr>
      <td style="padding-left:10px;"><strong>State Code:</strong> {{$whDetail->state_code}}</td>
    </tr>
    @endif
    @if(isset($whDetail->gstin) && !empty($whDetail->gstin))
    <tr>
      <td style="padding-left:10px;"><strong>GSTIN / UIN:</strong> {{$whDetail->gstin}}</td>
    </tr>
    @endif
    </table>
  </td>
  

  <td width="30%" align="center" valign="top" >
    <table width="100%" border="0" cellspacing="0" cellpadding="4" style="font-size:11px;">
     <tr bgcolor="#efefef">
      <td style=" border-bottom:1px solid #000;  padding:10px 8px;font-size:14px !important;"><strong>PO Details</strong></td>
    </tr>
    <?php 
        $poType = ($productArr[0]->po_type == 1 ? 'Qty Based' : 'Value Based');
        $paymentMode = ($productArr[0]->payment_mode == 2 ? 'Pre Paid' : 'Post Paid');
    ?>
    <tr>
    <td style="padding-left:10px;"><strong> PO No: </strong>{{$productArr[0]->po_code}}</td>
    </tr>
    <tr>
    <td style="padding-left:10px;"><strong>PO Date: </strong>{{Utility::dateFormat($productArr[0]->po_date)}}</td>
    </tr>
    <tr>
    <td style="padding-left:10px;"><strong>Delivery Date: </strong>{{Utility::dateFormat($productArr[0]->delivery_date)}}</td>
    </tr>
    <tr>
    <td style="padding-left:10px;"><strong>PO Type: </strong>@if($productArr[0]->indent_id)
    Indent- {{$indentCode}}
  @else
  Direct PO ({{$poType}})  
    @endif</td>
    
    </tr>    
    <tr>
        <td style="padding-left:10px;"><strong>Payment Mode: </strong>{{$paymentMode}}</td>
    </tr>
    @if($paymentType != '')
    <tr>
        <td style="padding-left:10px;"><strong>Payment Type: </strong>{{$paymentType}}</td>
    </tr>
    @endif
    @if($productArr[0]->tlm_name!='')
    <tr>
        <td style="padding-left:10px;"><strong>Account: </strong>{{$productArr[0]->tlm_name}}</td>
    </tr>
    @endif
    @if($productArr[0]->payment_refno!='')
    <tr>
        <td style="padding-left:10px;"><strong>Payment Ref. No: </strong>{{$productArr[0]->payment_refno}}</td>
    </tr>
    @endif
    @if($productArr[0]->payment_due_date!='' && $productArr[0]->payment_due_date!='0000-00-00 00:00:00')
    <tr>
        <td style="padding-left:10px;"><strong>Payment Due Date: </strong>{{Utility::dateFormat($productArr[0]->payment_due_date)}}</td>
    </tr>
    @endif
    @if($productArr[0]->logistics_cost!=0)
    <tr>
        <td style="padding-left:10px;"><strong>Logistics Cost: </strong>{{number_format($productArr[0]->logistics_cost,2)}}</td>
    </tr>
    @endif
    <tr>
        <td style="padding-left:10px;"><strong>Created By: </strong>{{$productArr[0]->user_name}}</td>
    </tr>
    <tr>
        <td style="padding-left:10px;"><strong>Status: </strong>{{$poStatus}}@if($productArr[0]->is_closed==1)
                                (Debit Note Created)
                                @endif</td>
    </tr>    
    @if($approvedStatus!='')
    <tr>
        <td style="padding-left:10px;"><strong>Approval Status: </strong>{{$approvedStatus}}</td>
    </tr>
    @endif
    
    </table>
  </td>
</tr>
</table>
<br>
<strong style="font-size:16px !important; ">Product Description</strong>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="right"<span style="float:right;font-size: 11px; font-weight: bold;">* All Amounts in (Rs.) </span></td>
    </tr>
</table>
<table width="100%" style=" font-size:11px !important; border-collapse:collapse; border-right:1px solid #000; margin-top:10px" cellspacing="0" cellpadding="5" border="1">

<tr bgcolor="#efefef" style="font-size:12px !important; font-weight:bold;">
  <thead>
  <td height="25">SKU</td>
  <td>Product Name</td>
  <th>HSN Code</th>
  <td>Qty</td>
  <td>Free Qty</td>
  <td  align="center">MRP</td>
  <td  align="center">Base&nbsp;Rate</td>
  <td  align="center">Taxable&nbsp;Value</td>
  <td  align="center">Tax%</td>
  <td  align="center">Tax Amount</td>
  <td  align="center">Total</td>
</tr>
</thead>
<?php 
  $sno = 1;
  $sumOfSubtotal = 0;
  $sumOfTaxtotal = 0;
  $sumOfGrandtotal = 0;
  $totQty = 0;
  $taxTypeAmt = array();

  $sumOfQty = 0;
  $sumOfFreeQty = 0;
  $taxper = 0;
  $sumOfTaxAmount =0;
  $sumofPrices = 0;
  $taxSummArr = array();
?>
@foreach($productArr as $product)
    <?php 
  if($productArr[0]->po_type==1){ $taxText = 0; }
  
  $uom = ($product->uom!=''&&$product->uom!=0) ? $packTypes[$product->uom] : 'Ea';
  $free_uom = ($product->free_uom!=''&&$product->free_uom!=0) ? $packTypes[$product->free_uom] : 'Ea';  
  $qty = ($product->qty!='') ? $product->qty : 0;
  $free_qty = ($product->free_qty!='') ? $product->free_qty : 0;
  $no_of_eaches = ($product->no_of_eaches==0 || $product->no_of_eaches=='') ? 1 : $product->no_of_eaches;
  $free_no_of_eaches = ($product->free_eaches==0 || $product->free_eaches=='') ? 1 : $product->free_eaches;
  // $basePrice = $product->unit_price;
  // $isTaxInclude = $product->is_tax_included;
 
    $basePrice = $product->price;
   $isTaxInclude = $product->is_tax_included;
   $unit_price = $product->unit_price;
   $totQty = ($qty*$no_of_eaches-$free_qty*$free_no_of_eaches);
    if($isTaxInclude==1){
        $basePrice = ($basePrice/(1+($product->tax_per/100)));
        $unit_price = ($unit_price/(1+($product->tax_per/100)));
    }
    $taxAmt = $product->tax_amt;
    $taxName = $product->tax_name;
    $taxPer = $product->tax_per;
  $mrp = $product->mrp;
  $totPrice = $unit_price*$totQty;
  $newPrClass = (isset($product->newPrClass))?$product->newPrClass:'';
  ?>

<tr>
  <td height="25" ><span {{$newPrClass}}>{{$product->sku}}</span></td>
  <td><span {{$newPrClass}}>{{$product->product_title}}</span></td>
  <td>{{$product->hsn_code}}</td>
  <td>{{$qty}} {{$uom}} {{($uom!='Ea') ? '('.$qty*$no_of_eaches.' Ea)' : ''}}</td>
  <td>{{$free_qty}} {{$free_uom}} {{($free_uom!='Ea') ? '('.$free_qty*$free_no_of_eaches.' Ea)' : ''}}</td>
  <td  align="right">{{number_format($mrp, 2)}}</td>
  <td  align="right">{{number_format(($basePrice), 3)}}</td>
  <td  align="right">{{number_format(($totPrice), 3)}}</td>
  <td  align="center">{{empty($product->tax_name) ? '' : $product->tax_name.' @ '}}{{(float)$product->tax_per}}</td>
  <td  align="right">{{number_format($product->tax_amt, 3)}}</td>
  <td  align="right">{{number_format(($product->sub_total), 3)}}</td>  
  

</tr>
      <?php 
      $sumOfTaxtotal = $sumOfTaxtotal + $taxAmt; 
      $sumOfSubtotal = $sumOfSubtotal + $product->sub_total;
      $sumofPrices  +=($totPrice); 
      $sumOfTaxAmount += $product->tax_amt;
      $sumOfQty = $sumOfQty + $product->qty;
      $sumOfFreeQty = $sumOfFreeQty + $free_qty;
      if(isset($tax['Tax Type']) && $tax['Tax Percentage']){
        $taxsum = isset($taxSummArr[$tax['Tax Type']][(string)$tax['Tax Percentage']]) ? $taxSummArr[$tax['Tax Type']][(string)$tax['Tax Percentage']] :0;
      $taxSummArr[$tax['Tax Type']][(string)$tax['Tax Percentage']] = $taxsum+$taxAmt;
      }
      //print_r($taxSummArr);die;
      ?>
@endforeach
<tr>
<td height="25"  align="right"></td>
<td  align="right"></td>
<td  align="center"></td>
<td  align="center"></td>
<td  align="center"></td>
<td  align="right"></td>
<td  align="right"><strong>Total</strong></td>
<td  align="right">{{number_format($sumofPrices, 3)}}</td>
<td  align="right"></td>
<td  align="right">{{number_format($sumOfTaxAmount, 3)}}</td>
<td  align="right">{{number_format($sumOfGrandtotal = ($sumOfSubtotal), 3)}}</td>
</tr>


</table>
<br />
<?php
$taxPerr = '';
foreach($taxBreakup as $tax1){
    $taxPerr = ($taxPerr=='')?$tax1['tax']:$taxPerr;
}
?>
<table width="100%" border="0" cellspacing="5" cellpadding="0">
    <tr>
        <td width="50%" align="left" valign="top">
            <table cellpadding="5" cellspacing="0"  border="1" width="100%" style="font-size:11px !important; border-collapse:collapse; border-right:1px solid #000;">
                <tr bgcolor="#efefef" style="font-size:12px !important; font-weight:bold;">
                    <th width="33%" align="center" valign="middle">Tax Type</th>
                    @if($taxPerr!='')
                    <th width="33%" align="center" valign="middle">Tax%</th>
                    @endif
                    <th width="33%" align="center" valign="middle">Tax Amount</th>
                </tr>
                @foreach($taxBreakup as $tax)
                @if($tax['name']!='' && $tax['tax_value']>0)
                <tr>
                    <td width="33%" align="center" valign="middle" style="font-size:12px;">{{$tax['name']}}</td>
                    @if(isset($tax['tax']) && $tax['tax']!='')
                    <td width="33%" align="center" valign="middle" style="font-size:12px;">{{(float)$tax['tax']}}%</td>
                    @endif
                    <td width="33%" align="right" valign="middle" style="font-size:12px;">{{number_format(($tax['tax_value']), 3)}}</td>
                </tr>
                @endif
                @endforeach
            </table>
        </td>
        <td width="50%" align="left" valign="top">
            <table width="100%" style="font-size:11px !important; border-collapse:collapse; border-right:1px solid #000;" cellspacing="0" cellpadding="5" border="1">
                <tr bgcolor="#efefef" style="font-size:12px !important; font-weight:bold;">
                    <th width="33%" align="center" valign="middle">Total Price</th>
                    <th width="33%" align="center" valign="middle">Total Tax</th>
                    <th width="33%" align="center" valign="middle">Grand Total</th>
                </tr>
                <tr>
                    <td width="33%" align="right" valign="middle" style="font-size:12px;">{{number_format($sumofPrices, 3)}}</td>
                    <td width="33%" align="right" valign="middle" style="font-size:12px;">{{number_format($sumOfTaxAmount, 3)}}</td>
                    <td width="33%" align="right" valign="middle">{{number_format($sumOfGrandtotal = ($sumOfSubtotal), 3)}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

@if(!empty($productArr[0]->po_remarks))
<br>
<table width="100%" border="0" align="left" cellpadding="3" cellspacing="0">
  <tr>
    <td align="left" valign="middle"><strong style="font-size:12px;">Remarks:</strong></td>
  </tr>
    <tr>
    <td align="left" valign="middle" style="font-size:12px;">{{$productArr[0]->po_remarks}}</td>
  </tr>

</table>
@endif
