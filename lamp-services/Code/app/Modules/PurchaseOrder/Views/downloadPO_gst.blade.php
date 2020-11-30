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
        <td width="100%" align="left" valign="middle">
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
        <td align="center"><h4>Purchase Order</h4></td>
    </tr>
</table>

<table width="100%" border="1" cellspacing="0" cellpadding="4" bordercolor="#000" style=" font-size:12px !important;">
    <tr style="font-size:16px; text-align:left" class="hedding1 table-headings">
        <th width="25%">Supplier</th>
        <th width="25%">Receiver (Billed to)</th>
        <th width="25%">Consignee (Shipped To)s</th>
        <th width="25%">PO Details</th>
    </tr>
    <tr>
        <td valign="top" style="font-size:15px;">
            <strong>Name:</strong> {{$supplier->business_legal_name}}<br>
            <strong>Address:</strong> {{$supplier->address1}}, <br/> <?php if ($supplier->address2 != "") { ?>{{$supplier->address2}}<br />,<?php } ?>
            {{$supplier->city}}, {{$supplier->state_name}} {{$supplier->country_name}}, {{$supplier->pincode}}<br>
            <strong>Phone:</strong> {{(isset($userInfo->mobile_no) ? $userInfo->mobile_no : '')}}<br>
            <strong>Email:</strong> {{(isset($userInfo->email_id) ? $userInfo->email_id : '')}}<br>
            @if(!empty($supplier->sup_bank_name))
            <strong>Bank Name:</strong> {{$supplier->sup_bank_name}}<br>
            @endif
            @if(!empty($supplier->sup_account_no))
            <strong>A/c No:</strong> {{$supplier->sup_account_no}}<br>
            @endif
            @if(!empty($supplier->sup_account_name))
            <strong>A/c Name:</strong> {{$supplier->sup_account_name}}<br>
            @endif
            @if(!empty($supplier->sup_ifsc_code))
            <strong>IFSC Code:</strong> {{$supplier->sup_ifsc_code}}
            @endif
            <strong>State:</strong> @if(isset($supplier->state_name) && !empty($supplier->state_name)){{$supplier->state_name}}@endif<br>
            <strong>State Code:</strong> @if(isset($supplier->state_code) && !empty($supplier->state_code)){{$supplier->state_code}}@endif<br>
            <strong>PAN:</strong> @if(!empty($supplier->pan_number)){{$supplier->pan_number}}@endif<br>
            <strong>GSTIN / UIN:</strong> @if(isset($supplier->gstin) && !empty($supplier->gstin)){{$supplier->gstin}}@endif<br>
            <strong>FSSAI NO:</strong> @if(isset($supplier->fssai) && !empty($supplier->fssai)){{$supplier->fssai}}@endif                          
        </td>
        <td valign="top" style="font-size:15px;">
            <strong>Name:</strong> {{$whDetail->business_legal_name}}<br>
            <strong>Address:</strong> {{$whDetail->address1}}, <br> <?php if ($whDetail->address2 != "") { ?>{{$whDetail->address2}}<br />,<?php } ?>
            {{$whDetail->city}}, {{$whDetail->state_name}}, {{(empty($whDetail->country_name) ? 'India' : $whDetail->country_name)}} - {{$whDetail->pincode}}<br>
            <strong>State:</strong> @if(isset($whDetail->state_name) && !empty($whDetail->state_name)){{$whDetail->state_name}}@endif<br>
            <strong>State Code:</strong> @if(isset($whDetail->state_code) && !empty($whDetail->state_code)){{$whDetail->state_code}}@endif<br>
            <strong>GSTIN / UIN:</strong> @if(isset($whDetail->gstin) && !empty($whDetail->gstin)){{$whDetail->gstin}}@endif<br>
            <strong>FSSAI NO:</strong> @if(isset($whDetail->fssai) && !empty($whDetail->fssai)){{$whDetail->fssai}}@endif
        </td>
        <td valign="top" style="font-size:15px;">
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
        <td valign="top" style="font-size:15px;">
            <?php
            $poType = ($productArr[0]->po_type == 1 ? 'Qty Based' : 'Value Based');
            $paymentMode = ($productArr[0]->payment_mode == 2 ? 'Pre Paid' : 'Post Paid');
            ?>
            <strong>PO No:</strong> {{$productArr[0]->po_code}}<br>
            <strong>PO Date:</strong> {{Utility::dateFormat($productArr[0]->po_date)}}<br>
            <strong>Delivery Date:</strong> {{Utility::dateFormat($productArr[0]->delivery_date)}}<br>
            <strong>PO Type :</strong> @if($productArr[0]->indent_id)
            Indent- {{$indentCode}}
            @else
            Direct PO ({{$poType}})	
            @endif
            <br>
            <strong>Payment Mode:</strong> {{$paymentMode}}<br>
            @if($paymentType != '')
            <strong>Payment Type:</strong> {{$paymentType}}<br>
            @endif
            @if($productArr[0]->tlm_name!='')
            <strong>Account:</strong> {{$productArr[0]->tlm_name}}<br>
            @endif
            @if($productArr[0]->payment_refno!='')
            <strong>Payment Ref. No:</strong> {{$productArr[0]->payment_refno}}<br>
            @endif
            @if($productArr[0]->payment_due_date!='' && $productArr[0]->payment_due_date!='0000-00-00 00:00:00')
            <strong>Payment Due Date:</strong> {{Utility::dateFormat($productArr[0]->payment_due_date)}}<br>
            @endif
            @if($productArr[0]->logistics_cost!=0)
            <strong>Logistics Cost:</strong> {{number_format($productArr[0]->logistics_cost,2)}}<br>
            @endif
            <strong>Created By:</strong> {{$productArr[0]->user_name}}<br>
            <strong>Status:</strong> {{$poStatus}}@if($productArr[0]->is_closed==1)
            (Debit Note Created)
            @endif<br>
            @if($approvedStatus!='')
            <strong>Approval Status:</strong> {{$approvedStatus}}<br>
            @endif
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
<table style="font-size:12px;border-collapse: collapse; padding: 4px;" width="100%" cellspacing="0" cellpadding="0" border="1">
    <thead>
    <tr style="font-weight:bold;" class="thh">
        <th rowspan="2" align="left">S&nbsp;No</th>
        <th rowspan="2" align="left">Product&nbsp;Name</th>
        <th rowspan="2" align="left">HSN<br>Code</th>
        <th rowspan="2" align="right">MRP</th>
        <th rowspan="2" align="right">Rate</th>
        <th rowspan="2" align="center">Qty</th>
        <th rowspan="2" align="center">Free<br>Qty</th>
        <th rowspan="2" align="right">Taxable<br>Value</th>
        <th rowspan="2" align="right">Tax<br>Rate</th>
        <th rowspan="2" align="right">Tax<br>Amt</th>
        <th colspan="2" align="center">CGST</th>
        <th colspan="2" align="center">SGST/UTGST</th>
        <th colspan="2" align="center">IGST</th>
        <th rowspan="2" align="right"> Total </th>
    </tr>
    <tr style="font-weight:bold;">
        <td class="thh" align="right">%</td>
        <td class="thh" align="right">Amt</td>
        <td class="thh" align="right">%</td>
        <td class="thh" align="right">Amt</td>
        <td class="thh" align="right">%</td>
        <td align="center"><span class="thh">Amt</span></td>
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
    $sumOfTaxAmount = 0;
    $sumofPrices = 0;
    $taxSummArr = array();
    $taxSummary=array();
    $totCGST = $totSGST = $totIGST = $totUTGST = 0;
    ?>
    @foreach($productArr as $product)
    <?php
    if ($productArr[0]->po_type == 1) {
        $taxText = 0;
    }

    $uom = ($product->uom != '' && $product->uom != 0) ? $packTypes[$product->uom] : 'Ea';
    $free_uom = ($product->free_uom != '' && $product->free_uom != 0) ? $packTypes[$product->free_uom] : 'Ea';
    $qty = ($product->qty != '') ? $product->qty : 0;
    $free_qty = ($product->free_qty != '') ? $product->free_qty : 0;
    $no_of_eaches = ($product->no_of_eaches == 0 || $product->no_of_eaches == '') ? 1 : $product->no_of_eaches;
    $free_no_of_eaches = ($product->free_eaches == 0 || $product->free_eaches == '') ? 1 : $product->free_eaches;
    // $basePrice = $product->unit_price;
    // $isTaxInclude = $product->is_tax_included;

    $basePrice = $product->price;
    $isTaxInclude = $product->is_tax_included;
    $unit_price = $product->unit_price;
    $totQty = ($qty * $no_of_eaches - $free_qty * $free_no_of_eaches);
    if ($isTaxInclude == 1) {
        $basePrice = ($basePrice / (1 + ($product->tax_per / 100)));
        $unit_price = ($unit_price / (1 + ($product->tax_per / 100)));
    }
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
<tbody>
    <tr>
        <td align="center">{{$sno++}}</td>
        <td align="left" style="white-space:normal;"><span {{$newPrClass}}>{{$product->product_title}}</span></td>
        <td align="left">{{$product->hsn_code}}</td>
        <td align="right">{{number_format($product->mrp, 2)}}</td>
        <td align="right">{{number_format(($basePrice), 4)}}</td>
        <td align="center">{{$qty}} {{$uom}} {{($uom!='Ea') ? '('.$qty*$no_of_eaches.' Ea)' : ''}}</td>
        <td align="center">{{$free_qty}} {{$free_uom}} {{($free_uom!='Ea') ? '('.$free_qty*$free_no_of_eaches.' Ea)' : ''}}</td>
        <td align="right">{{number_format(($totPrice), 2)}}</td>
        <td align="right">{{(float)$product->tax_per}}</td>
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
        <td align="right">{{number_format(($product->sub_total), 2)}}</td>
    </tr>
    <?php
    $sumOfTaxtotal = $sumOfTaxtotal + $taxAmt;
    $sumOfSubtotal = $sumOfSubtotal + $product->sub_total;
    $sumofPrices +=($totPrice);
    $sumOfTaxAmount += $product->tax_amt;
    $sumOfQty = $sumOfQty + $product->qty;
    $sumOfFreeQty = $sumOfFreeQty + $free_qty;

     $taxprecent=isset($product->tax_per)?$product->tax_per:0;
    //print_r($taxprecent);die;
    if(array_key_exists($taxprecent,$taxSummary))
    {
      
        $taxSummary[$taxprecent]['tax_amount']+=$product->tax_amt;
        $taxSummary[$taxprecent]['taxable_amount']+= $totPrice;
        $taxSummary[$taxprecent]['tax_name']=$product->tax_name;
  ;
    }
    else
    {
        $taxSummary[$taxprecent]['tax_amount']=$product->tax_amt;
        $taxSummary[$taxprecent]['taxable_amount']= $totPrice;
        $taxSummary[$taxprecent]['tax_name']=$product->tax_name;
    }


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
        <td align="right"><strong>{{number_format($sumOfGrandtotal = ($sumOfSubtotal), 2)}}</strong></td>
    </tr>

</tbody>
</table>
<br />
<table cellpadding="1" cellspacing="0"class="table table-striped table-bordered table-advance table-hover" style="margin-top: 10px; width:100%;word-wrap:break-word;font-size:13px; float: right; border: 0;">

   <tr class="hedding1 table-headings">

   <th style="border: 0px !important;background: white !important;width:70%;    padding-left: 2px;" align="left">Grand Total In Words: <?php echo Utility::convertNumberToWords(round($sumOfSubtotal,2)); ?> <br>
   <span style="font-weight:normal">* Reverse Charges not Applicable</span> 
   
   </th>
        <th align="left" border="1" style="border: 1px solid !important;">Taxable <br> Value</th>        
        <th align="left" border="1" style="border: 1px solid !important;">Tax Type</th>
        <th align="left" border="1" style="border: 1px solid !important;">Tax<br>Rate</th>
        <th align="left" border="1" style="border: 1px solid !important;">Tax Amount</th>
    </tr>


     @foreach($taxSummary as $taxSumry=> $taxval )
        <tr style="font-size: 9px;">
            <td style="border: 0px !important;background: white !important;"></td>  
            <td align="right" style="border: 1px solid;font-size:13px; "><?php echo round($taxval['taxable_amount'],2); ?></td>
            <td align="left" style="border: 1px solid;font-size:13px; "><?php echo $taxval['tax_name']; ?></td>
            <td align="right" style="border: 1px solid;font-size:13px; "><?php echo round($taxSumry,0).'%'; ?></td>
            <td align="right" style="border: 1px solid;font-size:13px; "><?php echo round($taxval['tax_amount'],2); ?></td>      
        </tr>
     @endforeach
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
