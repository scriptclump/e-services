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
        <td width="50%" align="left" valign="middle"><img src="{{url('/')}}/img/ebutor.png" alt="" height="42" width="42"  style="float:left" > <strong style="float:left; line-height:42px;"> {{$leDetail->business_legal_name}}</strong></td>
        <td width="50%" align="right" valign="middle">
            
        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center"><h4>PURCHASE INVOICE</h4></td>
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
                    <td style=" border-bottom:1px solid #000;  padding:10px 8px;font-size:14px !important;"><strong>Delivery Address</strong></td>
                </tr>
                <tr>
                    <td style="padding-left:10px;"><strong>Name:</strong> {{$whDetail->lp_wh_name}}</td>
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
        <td width="25%" align="center" valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="4" style="font-size:11px;">
            <tr bgcolor="#efefef">
              <td style=" border-bottom:1px solid #000;  padding:10px 8px;font-size:14px !important;"><strong>Billing Address</strong></td>
            </tr>
            <tr>
              <td style="padding-left:10px;"><strong>Name:</strong> {{$leDetail->business_legal_name}}</td>
            </tr>
            </tr>
            <tr>
              <td style="padding-left:10px;"><strong>Address:</strong> {{$leDetail->address1}},
                  <?php if ($leDetail->address2 != "") { ?></br> {{$leDetail->address2}},<?php } ?>
                <br>{{$leDetail->city}},{{$leDetail->state_name}}, {{(empty($leDetail->country_name) ? 'India' : $leDetail->country_name)}} - {{$leDetail->pincode}}
              </td>
            </tr>
            @if(isset($leDetail->state_name) && !empty($leDetail->state_name))
            <tr>
              <td style="padding-left:10px;"><strong>State:</strong> {{$leDetail->state_name}}</td>
            </tr>
            @endif
            @if(isset($leDetail->state_code) && !empty($leDetail->state_code))
            <tr>
              <td style="padding-left:10px;"><strong>State Code:</strong> {{$leDetail->state_code}}</td>
            </tr>
            @endif
            @if(isset($leDetail->gstin) && !empty($leDetail->gstin))
            <tr>
              <td style="padding-left:10px;"><strong>GSTIN / UIN:</strong> {{$leDetail->gstin}}</td>
            </tr>
            @endif
            @if(isset($leDetail->fssai) && !empty($leDetail->fssai))
            <tr>
              <td style="padding-left:10px;"><strong>FSSAI NO:</strong> {{$leDetail->fssai}}</td>
            </tr>
            @endif
            </table>
          </td>
        <td width="25%" align="center" valign="top" >
            <table width="100%" border="0" cellspacing="0" cellpadding="4" style="font-size:11px;">
                <tr bgcolor="#efefef">
                    <td style=" border-bottom:1px solid #000;  padding:10px 8px;font-size:14px !important;"><strong>Invoice Details</strong></td>
                </tr>
                <tr>
                    <td style="padding-left:10px;"><strong> Invoice Code: </strong>{{$productArr[0]->invoice_code}}</td>
                </tr>
                <tr>
                    <td style="padding-left:10px;"><strong>Invoice Date: </strong>{{Utility::dateFormat($productArr[0]->invoice_date)}}</td>
                </tr>    
                <tr>
                    <td style="padding-left:10px;"><strong> GRN Code: </strong>{{$productArr[0]->inward_code}}</td>
                </tr>
                <tr>
                    <td style="padding-left:10px;"><strong>GRN Date: </strong>{{Utility::dateFormat($productArr[0]->inward_date)}}</td>
                </tr>
                <tr>
                    <td style="padding-left:10px;"><strong>PO Code: </strong>{{$productArr[0]->po_code}}</td>
                </tr>    
                <tr>
                    <td style="padding-left:10px;"><strong>Created By: </strong>{{$productArr[0]->user_name}}</td>
                </tr>
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
<thead>
    <tr bgcolor="#efefef" style="font-size:12px !important; font-weight:bold;">
        <td height="25">SKU</td>
        <td >Product Name</td>  
        <th>HSN Code</th>
        <td >Qty(Ea)</td>
        <td>Free Qty(Ea)</td>
        <td align="center">MRP</td>
        <td align="center">Unit&nbsp;Base&nbsp;Rate</td>
        <td align="center">Sub&nbsp;Total</td>
        <td align="center">Tax%</td>
        <td align="right">Tax Amount</td>
        <td align="center">Discount</td>
        <td align="right">Total</td>
    </tr>
</thead>
    <?php
    $sno = 1;
    $sumOfSubtotal = 0;
    $sumOfGrandtotal = 0;
    $totQty = 0;
    $taxTypeAmt = array();


    $sumOfQty = 0;
    $sumOfFreeQty = 0;
    $taxper = 0;
    $sumOfTaxAmount = 0;
    $sumofPrices = 0;
    $taxSummArr = array();
    $sumOfGrandtotal = (isset($productArr[0]->grand_total))?$productArr[0]->grand_total:0;
    $totDiscount=(isset($productArr[0]->discount_on_total))?$productArr[0]->discount_on_total:0;
    $shipping_fee=(isset($productArr[0]->shipping_fee))?$productArr[0]->shipping_fee:0;
    ?>
    @foreach($productArr as $product)
    <?php
    $qty = ($product->qty != '') ? $product->qty : 0;
    $free_qty = ($product->free_qty != '') ? $product->free_qty : 0;
    $basePrice = $product->price;
    $unit_price = $product->unit_price;
    $totQty = ($qty - $free_qty);
    $taxAmt = $product->tax_amt;
    $taxName = $product->tax_name;
    $taxPer = $product->tax_per;
    $mrp = $product->mrp;
    $totPrice = $unit_price * $totQty;
    $newPrClass = (isset($product->newPrClass)) ? $product->newPrClass : '';
    ?>
<tbody>
    <tr>
        <td height="25" ><span {{$newPrClass}}>{{$product->sku}}</span></td>
        <td><span {{$newPrClass}}>{{$product->product_title}}</span></td>
        <td>{{$product->hsn_code}}</td>
        <td>{{$qty}}</td>
        <td>{{$free_qty}}</td>
        <td  align="right">{{number_format($mrp, 2)}}</td>  
        <td  align="right">{{$unit_price}}</td>
        <td  align="right">{{number_format(($basePrice), 3)}}</td>
        <td  align="center">{{empty($product->tax_name) ? '' : $product->tax_name.' @ '}}{{(float)$product->tax_per}}</td>
        <td  align="right">{{number_format($product->tax_amt, 3)}}</td>
        <td align="right">{{$product->discount_amount}}</td>
        <td  align="right">{{number_format(($product->sub_total-$product->discount_amount), 3)}}</td>
    </tr>
    <?php
    $sumOfSubtotal = $sumOfSubtotal + ($product->sub_total-$product->discount_amount);
    $sumofPrices +=($totPrice);
    $totDiscount += $product->discount_amount;
    $sumOfTaxAmount += $product->tax_amt;
    $sumOfQty = $sumOfQty + $product->qty;
    $sumOfFreeQty = $sumOfFreeQty + $free_qty;
    if (isset($tax['Tax Type']) && $tax['Tax Percentage']) {
        $taxsum = isset($taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']]) ? $taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']] : 0;
        $taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']] = $taxsum + $taxAmt;
    }
    //print_r($taxSummArr);die;
    ?>
    @endforeach
    <tr>
        <td height="25"  align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="right"></td>
        <td align="right"><strong>Total</strong></td>
        <td align="right">{{number_format($sumofPrices, 3)}}</td>
        <td align="right"></td>
        <td align="right">{{number_format($sumOfTaxAmount, 3)}}</td>
        <td align="right"></td>
        <td align="right">{{number_format($sumOfSubtotal, 3)}}</td>
    </tr>
</tbody>
</table>

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
            <table width="100%" style=" font-size:11px !important; border-collapse:collapse; border-right:1px solid #000; margin-top:10px" cellspacing="0" cellpadding="5" border="1">
                <tr bgcolor="#efefef" style="font-size:12px !important; font-weight:bold;">
                    <td align="left" valign="middle"><strong style="font-size:12px;">Total Price</strong></td>
                    <td align="left" valign="middle"><strong style="font-size:12px;">Total Tax</strong></td>
                    @if($totDiscount!=0)
                        <td align="left" valign="middle"><strong style="font-size:12px;">Total Disc.</strong></td>
                    @endif
                    @if($shipping_fee!=0)
                       <td align="left" valign="middle"><strong style="font-size:12px;">Shipping Fee</strong></td>
                    @endif
                    <td align="left" valign="middle"><strong style="font-size:12px;">Grand Total</strong></td>
                </tr>
                <tr>
                    <td align="left" valign="middle" style="font-size:12px;">{{number_format($sumofPrices, 3)}}</td>
                    <td align="left" valign="middle" style="font-size:12px;">{{number_format($sumOfTaxAmount, 3)}}</td>
                    @if($totDiscount!=0)
                    <td align="left" valign="middle" style="font-size:12px;">{{number_format($totDiscount, 3)}}</td>
                    @endif
                    @if($shipping_fee!=0)
                    <td align="left" valign="middle" style="font-size:12px;">{{number_format($shipping_fee, 3)}}</td>
                    @endif
                    <td align="left" valign="middle">{{number_format($sumOfGrandtotal, 3)}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
