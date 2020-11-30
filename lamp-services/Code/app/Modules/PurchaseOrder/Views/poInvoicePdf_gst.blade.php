<style>
    body {
        margin: 0px;
        padding: 0px;
        color: #333;
        font-family: "Open Sans",sans-serif !important;
    }
    table{ border-collapse: collapse;}
    .thh{ background: #efefef;}
</style>
<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>
<table width="100%" border="0" cellspacing="5" cellpadding="5">
    <tr>
        <td width="50%" align="left" valign="middle">
            @if(isset($leDetail->logo) and !empty($leDetail->logo))
            <img src="{{$leDetail->logo}}" alt="" height="42" width="42"  style="float:left" > 
            @endif
            <strong style="float:left; line-height:42px;"> {{$leDetail->business_legal_name}}</strong>
        </td>
        <td width="50%" align="right" valign="middle">

        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center"><h4>PURCHASE INVOICE</h4></td>
    </tr>
</table>

<table width="100%" border="1" cellspacing="0" cellpadding="4" bordercolor="#000" style=" font-size:12px !important;">
    <tr bgcolor="#efefef">
        <td width="25%" align="left" valign="bottom"><strong>Supplier</strong></td>
        <td width="25%" align="left" valign="bottom"><strong>Receiver (Billed To)</strong></td>
        <td width="25%" align="left" valign="bottom"><strong>Consignee (Shipped To)</strong></td>
        <td width="25%" align="left" valign="bottom"><strong>Invoice Details</strong></td>
    </tr>
    <tr>
        <td align="left" valign="top">
            <strong>Name:</strong> {{$supplier->business_legal_name}}<br>
            <strong>Address:</strong> {{$supplier->address1}}, <br/> <?php if ($supplier->address2 != "") { ?>{{$supplier->address2}}<br />,<?php } ?>
            {{$supplier->city}}, {{$supplier->state_name}} {{$supplier->country_name}}, {{$supplier->pincode}}<br>
            <strong>Phone:</strong> {{(isset($userInfo->mobile_no) ? $userInfo->mobile_no : '')}}<br>
            <strong>Email:</strong> {{(isset($userInfo->email_id) ? $userInfo->email_id : '')}}<br>                        
            <strong>State:</strong> @if(isset($supplier->state_name) && !empty($supplier->state_name)){{$supplier->state_name}}@endif<br>                        
            <strong>State Code:</strong> @if(isset($supplier->state_code) && !empty($supplier->state_code)){{$supplier->state_code}}@endif<br>
            <strong>PAN:</strong> @if(!empty($supplier->pan_number)){{$supplier->pan_number}}@endif<br>
            <strong>GSTIN / UIN:</strong> @if(isset($supplier->gstin) && !empty($supplier->gstin)){{$supplier->gstin}}
            @endif<br>
            <strong>FSSAI NO:</strong> @if(isset($supplier->fssai) && !empty($supplier->fssai)){{$supplier->fssai}}
            @endif
        </td>
        <td align="left" valign="top">
            <strong>Name:</strong> {{$leDetail->business_legal_name}}<br>
            <strong>Address:</strong> {{$leDetail->address1}}, <br> <?php if ($leDetail->address2 != "") { ?>{{$leDetail->address2}}<br />,<?php } ?>
            {{$leDetail->city}}, {{$leDetail->state_name}}, {{(empty($leDetail->country_name) ? 'India' : $leDetail->country_name)}} - {{$leDetail->pincode}}<br>
            <strong>State:</strong> @if(isset($leDetail->state_name) && !empty($leDetail->state_name)){{$leDetail->state_name}}@endif<br>
            <strong>State Code:</strong> @if(isset($leDetail->state_code) && !empty($leDetail->state_code)){{$leDetail->state_code}}@endif<br>
            <strong>GSTIN / UIN:</strong> @if(isset($leDetail->gstin) && !empty($leDetail->gstin)){{$leDetail->gstin}}@endif<br>
            <strong>FSSAI NO:</strong> @if(isset($leDetail->fssai) && !empty($leDetail->fssai)){{$leDetail->fssai}}@endif
        </td>
        <td align="left" valign="top">
            @if(!empty($whDetail->le_wh_code))
            <strong>Code:</strong> {{$whDetail->le_wh_code}}<br>
            @endif
            <strong>Name:</strong> {{$whDetail->lp_wh_name}}<br>
            <strong>Address:</strong> {{$whDetail->address1}}, <br> <?php if ($whDetail->address2 != "") { ?>{{$whDetail->address2}}<br />,<?php } ?>
            {{$whDetail->city}}, {{$whDetail->state_name}}, {{$whDetail->country_name}} - {{$whDetail->pincode}}<br>
            @if(!empty($whDetail->landmark))
            <strong>Landmark: </strong> {{$whDetail->landmark}}
            @endif
            @if(!empty($whDetail->contact_name))
            <strong>Contact Person:</strong> {{$whDetail->contact_name}}
            @endif
            @if(!empty($whDetail->phone_no))
            <strong>Phone:</strong> {{$whDetail->phone_no}}<br>
            @endif
            @if(!empty($whDetail->email))
            <strong>Email:</strong> {{$whDetail->email}}<br>
            @endif                        
            <strong>State:</strong> @if(isset($whDetail->state_name) && !empty($whDetail->state_name)){{$whDetail->state_name}}@endif<br>                        
            <strong>State Code:</strong> @if(isset($whDetail->state_code) && !empty($whDetail->state_code)){{$whDetail->state_code}}@endif<br>
            <strong>GSTIN / UIN:</strong> @if(isset($whDetail->gstin) && !empty($whDetail->gstin)){{$whDetail->gstin}}@endif<br>
            <strong>FSSAI NO:</strong> @if(isset($whDetail->fssai) && !empty($whDetail->fssai)){{$whDetail->fssai}}@endif
        </td>
        <td align="left" valign="top">
            <strong>Invoice code:</strong> {{$productArr[0]->invoice_code}}<br>
            <strong>Invoice Date:</strong> {{Utility::dateFormat($productArr[0]->invoice_date)}}<br>
            <strong>GRN code:</strong> {{$productArr[0]->inward_code}}<br>
            <strong>GRN Date:</strong> {{Utility::dateFormat($productArr[0]->inward_date)}}<br>
            <strong>PO code:</strong> {{$productArr[0]->po_code}}<br>
            <strong>Created By:</strong> {{$productArr[0]->user_name}}
        </td>
    </tr>
</table>
<br>




<strong style="font-size:13px !important; ">Product Description</strong>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="right"<span style="float:right;font-size: 11px; font-weight: bold;">* All Amounts in (Rs.) </span></td>
    </tr>
</table>

<table style="font-size:12px;border-collapse: collapse; padding: 4px;" width="100%" cellspacing="0" cellpadding="0" border="1">

        <thead>
        <tr style="font-weight:bold;" class="thh">
            <td rowspan="2" align="left">S&nbsp;No</td>
            <td rowspan="2" align="left">Product&nbsp;Name</td>
            <td rowspan="2" align="left">HSN<br>Code</td>
            <td rowspan="2" align="right">MRP</td>
            <td rowspan="2" align="right">Rate</td>
            <td rowspan="2" align="right">Qty</td>
            <td rowspan="2" align="right">Free<br>Qty</td>
            <td rowspan="2" align="right">Taxable<br>Value</td>
            <td rowspan="2" align="right">Tax<br>Rate</td>
            <td rowspan="2" align="right">Tax<br>Amt</td>
            <td colspan="2" align="center">CGST</td>
            <td colspan="2" align="center">SGST/UTGST</td>
            <td colspan="2" align="center">IGST</td>
            <td colspan="2" align="center">Disc.</td>
            <td rowspan="2" align="right">Total</td>
        </tr>
        <tr style="font-weight:bold;">
            <td class="thh" align="right">%</td>
            <td class="thh" align="right">Amt</td>
            <td class="thh" align="right">%</td>
            <td class="thh" align="right">Amt</td>
            <td class="thh" align="right">%</td>
            <td class="thh" align="center">Amt</td>
            <td class="thh" align="right">%</td>
            <td align="center"><span class="thh">Amt</span></td>
        </tr>
        </thead>
        <tbody>
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
        $sumOfGrandtotal = (isset($productArr[0]->grand_total)) ? $productArr[0]->grand_total : 0;
        $totDiscount = 0;
        $totDiscountonbill = (isset($productArr[0]->discount_on_total)) ? $productArr[0]->discount_on_total : 0;
        $shipping_fee = (isset($productArr[0]->shipping_fee)) ? $productArr[0]->shipping_fee : 0;
        $totCGST = $totSGST = $totIGST = $totUTGST = 0;
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

        $tax_data = json_decode($product->tax_data, true);
        //foreach ($tax_data as $key => $val) {
        $cgst = isset($tax_data[0]['CGST']) ? $tax_data[0]['CGST'] : 0;
        $sgst = isset($tax_data[0]['SGST']) ? $tax_data[0]['SGST'] : 0;
        $igst = isset($tax_data[0]['IGST']) ? $tax_data[0]['IGST'] : 0;
        $utgst = isset($tax_data[0]['UTGST']) ? $tax_data[0]['UTGST'] : 0;

        $cgstPer = ($product->tax_per * $cgst) / 100;
        $sgstPer = ($product->tax_per * $sgst) / 100;
        $igstPer = ($product->tax_per * $igst) / 100;
        $utgstPer = ($product->tax_per * $utgst) / 100;

        $cgst_val = ($product->tax_amt * $cgst) / 100;
        $sgst_val = ($product->tax_amt * $sgst) / 100;
        $igst_val = ($product->tax_amt * $igst) / 100;
        $utgst_val = ($product->tax_amt * $utgst) / 100;
        // }
        $totCGST = $totCGST + $cgst_val;
        $totSGST = $totSGST + $sgst_val;
        $totIGST = $totIGST + $igst_val;
        $totUTGST = $totUTGST + $utgst_val;
        ?>
        <tr>
            <td align="center">{{$sno++}}</td>
            <td><span {{$newPrClass}}>{{$product->product_title}}</span></td>
            <td>{{$product->hsn_code}}</td>
            <td align="right">{{number_format($mrp, 2)}}</td>  
            <td align="right">{{$unit_price}}</td>
            <td align="right">{{$qty}}</td>
            <td align="right">{{$free_qty}}</td>
            <td align="right">{{number_format(($basePrice), 2)}}</td>
            <td align="center">{{(float)$product->tax_per}}</td>
            <td align="right">{{number_format($product->tax_amt, 2)}}</td>
            <td align="right">{{$cgstPer}}</td>
            <td align="right">{{number_format($cgst_val,2)}}</td>
            @if($sgstPer!=0)
            <td align="right">{{$sgstPer}}</td>
            <td align="right">{{number_format($sgst_val,2)}}</td>
            @elseif($utgstPer!=0)
            <td align="right">{{$utgstPer}}</td>
            <td align="right">{{number_format($utgst_val,2)}}</td>
            @else
            <td align="right">0</td>
            <td align="right">0</td>
            @endif
            <td align="right">{{$igstPer}}</td>
            <td align="right">{{number_format($igst_val,2)}}</td>
            <td align="right">{{number_format($product->discount_per,2)}}</td>
            <td align="right">{{$product->discount_amount}}</td>
            <td align="right">{{number_format(($product->sub_total-$product->discount_amount), 2)}}</td>
        </tr>
        <?php
        $sumOfSubtotal = $sumOfSubtotal + ($product->sub_total - $product->discount_amount);
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
            <td colspan="5" align="right"></td>
            <td align="center"><!-- {{number_format($sumOfSubtotal, 2)}} --></td>
            <td><strong>Total</strong></td>
            <td align="right"><strong>{{number_format($sumofPrices, 2)}}</strong></td>
            <td align="center"><!-- {{number_format($sumOfSubtotal, 2)}} --></td>
            <td align="right"><strong>{{number_format($sumOfTaxAmount, 2)}}</strong></td>
            <td align="right"></td>
            <td align="right"><strong>{{number_format($totCGST, 2)}}</strong></td>
            @if($totSGST !=0)
            <td align="right"></td>
            <td align="right"><strong>{{number_format($totSGST, 2)}}</strong></td>
            @elseif($totUTGST !=0)
             <td align="right"></td>
            <td align="right"><strong>{{number_format($totUTGST, 2)}}</strong></td>
            @else
            <td align="right">0</td>
            <td align="right">0</td>
            @endif
            <td align="right"></td>
            <td align="right"><strong>{{number_format($totIGST, 2)}}</strong></td>
            <td align="right"></td>
            <td align="right"><strong>{{number_format($totDiscount, 2)}}</strong></td>
            <td align="right"><strong>{{number_format($sumOfSubtotal, 2)}}</strong></td>
        </tr>
    </tbody>
</table>
<?php
$taxPerr = '';
foreach ($taxBreakup as $tax1) {
    $taxPerr = ($taxPerr == '') ? $tax1['tax'] : $taxPerr;
}
?>
<table width="100%" border="0" cellspacing="5" cellpadding="0">
    <tr>
        <td width="50%" align="left" valign="top">
            <table cellpadding="2" cellspacing="2" class="table" width="100%">
                <tr>
                    <td style="word-wrap:break-word;font-size:12px;width:100%;font-weight:bold;">Grand Total In Words: <?php echo Utility::convertNumberToWords(round($sumOfGrandtotal,2)); ?></td>
                </tr>
                <tr>
                    <td style="word-wrap:break-word;font-size:11px;width:100%;">* Reverse Charges not Applicable</td>
                </tr>
            </table>
        </td>
        <td width="50%" align="left" valign="top">
            <table width="100%" style=" font-size:11px !important; border-collapse:collapse; border-right:1px solid #000; margin-top:10px" cellspacing="0" cellpadding="5" border="1">
                <tr style="font-size:12px !important; font-weight:bold;" class="thh">
                    <td align="right" valign="middle"><strong style="font-size:12px;">Total</strong></td>
                    <td align="right" valign="middle"><strong style="font-size:12px;">Bill Disc.</strong></td>
                    <td align="left" valign="middle"><strong style="font-size:12px;">Grand Total</strong></td>
                </tr>
                <tr>
                    <td align="right" valign="middle" style="font-size:12px;">{{number_format($sumOfSubtotal, 2)}}</td>
                    <td align="right" valign="middle" style="font-size:12px;">{{number_format($totDiscountonbill, 2)}}</td>
                    <td align="right" valign="middle">{{number_format($sumOfGrandtotal, 2)}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
