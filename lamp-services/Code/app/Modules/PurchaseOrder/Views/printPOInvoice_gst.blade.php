<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>
<html dir="ltr" lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Purchase Invoice</title>
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
            /*.table-bordered, .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td{padding:4px;}
            .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th{padding:5px;}
            .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #fbfcfd !important;
            -webkit-print-color-adjust: exact !important;
            }
            */.printmartop {margin-top: 10px;}
            .container {margin-top: 20px;}

            .small1 {font-size: 73%;}
            .small2 {font-size: 65.5%;}
            .bg {background-color: #efefef;padding: 8px 0px;}
            .bold{font-weight: bold;}


            .table-bordered>tbody>tr>td{border: 1px solid #000 !important;}
            .table-bordered>thead>tr>th{border: 1px solid #000 !important;}

            .page-break{ display: block !important; clear: both !important; page-break-after:always !important;}

            .table-headings th{background:#c0c0c0 !important; font-weight:bold !important; border:1px solid #000 !important;}
            .newproduct{color: blue;font-weight: bold !important;}
        </style>

    </head>
    <body>

        <div style="page-break-after: always;">
            <table width="100%" border="0" cellspacing="5" cellpadding="5" style=" word-wrap:break-word; font-size:13px;">
                <tr>
                    <td width="50%" align="left" valign="top">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                @if(isset($leDetail->logo) and !empty($leDetail->logo))
                                <td align="left" width="10%"><img src="{{$leDetail->logo}}" alt="" height="42" width="42" ></td>
                                @endif
                                <td align="left" width="90%"><strong style="padding-top:-20px;">{{$leDetail->business_legal_name}}</strong></td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%" align="right" valign="middle"></td>
                </tr>
            </table>
            <div style="margin:0px auto; text-align:center;">
                <h4>PURCHASE INVOICE</h4>
            </div> 
            <table width="100%" class="table table-bordered thline printtable " cellpadding="5" style=" word-wrap:break-word;">
                <tr style="font-size:13px; text-align:left" class="hedding1 table-headings">
                    <th width="25%">Details of Supplier</th>
                    <th width="25%">Receiver (Billed to)</th>
                    <th width="25%">Consignee (Shipped To)</th>                    
                    <th width="25%">Invoice Details</th>
                </tr>
                <tr style="font-size:13px;">
                    <td valign="top">
                        <strong>Name:</strong> {{$supplier->business_legal_name}}<br>
                        <strong>Address:</strong> {{$supplier->address1}}, <br/> <?php if ($supplier->address2 != "") { ?>{{$supplier->address2}}<br />,<?php } ?>
                        {{$supplier->city}}, {{$supplier->state_name}} {{$supplier->country_name}}, {{$supplier->pincode}}<br>
                        <strong>Phone:</strong> {{(isset($userInfo->mobile_no) ? $userInfo->mobile_no : '')}}<br>
                        <strong>Email:</strong> {{(isset($userInfo->email_id) ? $userInfo->email_id : '')}}<br>                        
                        <strong>State:</strong> @if(isset($supplier->state_name) && !empty($supplier->state_name)){{$supplier->state_name}}@endif<br>                        
                        <strong>State Code:</strong> @if(isset($supplier->state_code) && !empty($supplier->state_code)){{$supplier->state_code}}@endif<br>
                        <strong>PAN:</strong> @if(!empty($supplier->pan_number)){{$supplier->pan_number}}@endif<br>
                        <strong>GSTIN/UIN:</strong> @if(isset($supplier->gstin) && !empty($supplier->gstin)){{$supplier->gstin}}
                        @endif
                    </td>                    
                    <td valign="top">
                        <strong>Name:</strong> {{$leDetail->business_legal_name}}<br>
                        <strong>Address:</strong> {{$leDetail->address1}}, <br> <?php if ($leDetail->address2 != "") { ?>{{$leDetail->address2}}<br />,<?php } ?>
                        {{$leDetail->city}}, {{$leDetail->state_name}}, {{(empty($leDetail->country_name) ? 'India' : $leDetail->country_name)}} - {{$leDetail->pincode}}<br>
                        <strong>State:</strong> @if(isset($leDetail->state_name) && !empty($leDetail->state_name)){{$leDetail->state_name}}@endif<br>
                        <strong>State Code:</strong> @if(isset($leDetail->state_code) && !empty($leDetail->state_code)){{$leDetail->state_code}}@endif<br>
                        <strong>GSTIN/UIN:</strong> @if(isset($leDetail->gstin) && !empty($leDetail->gstin)){{$leDetail->gstin}}@endif
                    </td>
                    <td valign="top">
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
                        <strong>GSTIN/UIN:</strong> @if(isset($leDetail->gstin) && !empty($leDetail->gstin)){{$leDetail->gstin}}@endif
                    </td>
                    <td valign="top">
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
            <span style="float:right;font-size: 11px; font-weight: bold;">* All Amounts in ({{$curSymbol}}) </span>
            <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered table-advance table-hover" style="font-size:13px;">
                <thead>
                <tr class="hedding1 table-headings">
                    <th rowspan="2" align="left">S&nbsp;No</th>
                    <th rowspan="2" align="left">Product&nbsp;Name</th>
                    <th rowspan="2" align="left">HSN<br>Code</th>
                    <th rowspan="2" align="right">MRP</th>
                    <th rowspan="2" align="right">Rate</th>
                    <th rowspan="2" align="right">Qty</th>
                    <th rowspan="2" align="right">Free<br>Qty</th>
                    <th rowspan="2" align="right">Taxable<br>Value</th>
                    <th rowspan="2" align="right">Tax<br>Rate</th>
                    <th rowspan="2" align="right">Tax<br>Amt</th>
                    <th colspan="2" align="center">CGST</th>
                    <th colspan="2" align="center">SGST/UTGST</th>
                    <th colspan="2" align="center">IGST</th>
                    <th colspan="2" align="center"> Disc. </th>
                    <th rowspan="2" align="right"> Total </th>
                </tr>
                <tr class="hedding1 table-headings">
                    <th colspan="1" rowspan="1">%</th>
                    <th colspan="1" rowspan="1">Amt</th>
                    <th colspan="1" rowspan="1">%</th>
                    <th colspan="1" rowspan="1">Amt</th>
                    <th colspan="1" rowspan="1">%</th>
                    <th colspan="1" rowspan="1">Amt</th>
                    <th colspan="1" rowspan="1">%</th>
                    <th colspan="1" rowspan="1">Amt</th>
                </tr>
            </thead>
                <?php
                $sno = 1;
                $sumOfSubtotal = 0;
                $sumOfTaxtotal = 0;
                $sumOfGrandtotal = 0;
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
                $sumTax = 0;
                $taxText = '';

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
                <tbody>
                <tr class="odd gradeX">
                    <td align="center">{{$sno++}}</td>
                    <td align="left" style="word-wrap:break-word; width:14%;"><span {{$newPrClass}}>{{$product->product_title}}</span></td>
                    <td align="left">{{$product->hsn_code}}</td>
                    <td align="right">{{number_format($product->mrp, 2)}}</td>
                    <td align="right">{{$unit_price}}</td>
                    <td align="right">{{$qty}}</td>
                    <td align="right">{{$free_qty}}</td>
                    <td align="right">{{number_format(($basePrice), 2)}}</td>
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
                    <td align="right">{{number_format($product->discount_per,2)}}</td>
                    <td align="right">{{number_format($product->discount_amount,2)}}</td>
                    <td align="right">{{number_format(($product->sub_total-$product->discount_amount), 2)}}</td>
                </tr>
                <?php
                $sumOfTaxtotal = $sumOfTaxtotal + $product->tax_amt;
                $sumOfSubtotal = $sumOfSubtotal + ($product->sub_total - $product->discount_amount);
                $totDiscount += $product->discount_amount;
                $sumofPrices +=($totPrice);
                $sumOfTaxAmount += $product->tax_amt;
                if (isset($tax['Tax Type']) && $tax['Tax Percentage']) {
                    $taxsum = isset($taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']]) ? $taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']] : 0;
                    $taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']] = $taxsum + $taxAmt;
                }
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
            <br>
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
                        <table cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-advance table-hover" style=" word-wrap:break-word;font-size:12px;">
                            <tr class="hedding1 table-headings">
                                <th align="right">Total</th>
                                <th align="right">Bill Disc.</th>
                                <th align="right">Grand Total</th>
                            </tr>
                            <tr class="odd gradeX">
                                <td align="right">{{number_format($sumOfSubtotal, 2)}}</td>
                                <td align="right">{{number_format($totDiscountonbill, 2)}}</td>
                                <td align="right">{{number_format($sumOfGrandtotal, 2)}}</td>
                            </tr>
                        </table>                        
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
