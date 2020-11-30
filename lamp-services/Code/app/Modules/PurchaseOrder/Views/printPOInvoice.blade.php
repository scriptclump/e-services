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
            <table width="100%" border="0" cellspacing="5" cellpadding="5" style=" word-wrap:break-word; white-space:nowrap;font-size:13px;">
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
                <h4>Purchase Invoice</h4>
            </div> 
            <table width="100%" class="table table-bordered thline printtable " cellpadding="5" style=" word-wrap:break-word;font-size:13px;">
                <tr style="font-size:16px; text-align:left" class="hedding1 table-headings">
                    <th width="25%">Supplier</th>
                    <th width="25%">Shipping Address</th>
                    <th width="25%">Billing Address</th>
                    <th width="25%">Invoice Details</th>
                </tr>
                <tr>
                    <td valign="top" style="font-size:15px;">
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
                        <strong>GSTIN / UIN:</strong> @if(isset($leDetail->gstin) && !empty($leDetail->gstin)){{$leDetail->gstin}}@endif<br>
                        <strong>FSSAI NO:</strong> @if(isset($leDetail->fssai) && !empty($leDetail->fssai)){{$leDetail->fssai}}@endif
                    </td>
                    <td valign="top" style="font-size:15px;">
                        <strong>Name:</strong> {{$leDetail->business_legal_name}}<br>
                        <strong>Address:</strong> {{$leDetail->address1}}, <br> <?php if ($leDetail->address2 != "") { ?>{{$leDetail->address2}}<br />,<?php } ?>
                        {{$leDetail->city}}, {{$leDetail->state_name}}, {{(empty($leDetail->country_name) ? 'India' : $leDetail->country_name)}} - {{$leDetail->pincode}}<br>
                        <strong>State:</strong> @if(isset($leDetail->state_name) && !empty($leDetail->state_name)){{$leDetail->state_name}}@endif<br>
                        <strong>State Code:</strong> @if(isset($leDetail->state_code) && !empty($leDetail->state_code)){{$leDetail->state_code}}@endif<br>
                        <strong>GSTIN / UIN:</strong> @if(isset($leDetail->gstin) && !empty($leDetail->gstin)){{$leDetail->gstin}}@endif<br>
                        <strong>FSSAI NO:</strong> @if(isset($leDetail->fssai) && !empty($leDetail->fssai)){{$leDetail->fssai}}@endif
                    </td>
                    <td valign="top" style="font-size:15px;">
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
            <table cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-advance table-hover" style="white-space:nowrap;font-size:14px;">
                <thead>
                <tr class="hedding1 table-headings">
                    <th height="30">S&nbsp;No</th>
                    <th>SKU</th> 
                    <th>Product&nbsp;Name</th>
                    <th>HSN&nbsp;Code</th>
                    <th>Qty(Ea)</th>
                    <th>Free&nbsp;Qty(Ea)</th>
                    <th>MRP</th>
                    <th>Unit&nbsp;Base&nbsp;Rate</th>
                    <th>Taxable&nbsp;Value</th>
                    <th>Tax%</th>
                    <th>Tax&nbsp;Amount</th>
                    <th>Discount</th>
                    <th>Total</th>
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
                $sumOfGrandtotal = (isset($productArr[0]->grand_total))?$productArr[0]->grand_total:0;
                $totDiscount=(isset($productArr[0]->discount_on_total))?$productArr[0]->discount_on_total:0;
                $shipping_fee=(isset($productArr[0]->shipping_fee))?$productArr[0]->shipping_fee:0;
                ?>
                @foreach($productArr as $product)
                <?php
                $sumTax = 0;
                $taxText = '';
                ?>


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
                $newPrClass = (isset($product->newPrClass))?$product->newPrClass:'';
                ?>

                <tr class="odd gradeX">
                    <td align="center">{{$sno++}}</td>
                    <td><span {{$newPrClass}}>{{$product->sku}}</span></td>
                    <td style="white-space:normal;"><span {{$newPrClass}}>{{$product->product_title}}</span></td>
                    <td>{{$product->hsn_code}}</td>
                    <td>{{$qty}}</td>
                    <td>{{$free_qty}}</td>
                    <td align="right">{{number_format($product->mrp, 3)}}</td>
                    <td align="right">{{$unit_price}}</td>
                    <td align="right">{{number_format(($basePrice), 3)}}</td>
                    <td align="left">{{empty($product->tax_name) ? '' : $product->tax_name.' @ '}}{{(float)$product->tax_per}}    </td>
                    <td align="right">{{number_format($product->tax_amt, 3)}}</td>
                    <td align="right">{{$product->discount_amount}}</td>
                    <td align="right">{{number_format(($product->sub_total-$product->discount_amount), 3)}}</td>
                </tr>
                <?php
                $sumOfTaxtotal = $sumOfTaxtotal + $product->tax_amt;
                $sumOfSubtotal = $sumOfSubtotal + ($product->sub_total-$product->discount_amount);
                $totDiscount += $product->discount_amount;
                $sumofPrices +=($totPrice);
                $sumOfTaxAmount += $product->tax_amt;
                if (isset($tax['Tax Type']) && $tax['Tax Percentage']) {
                    $taxsum = isset($taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']]) ? $taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']] : 0;
                    $taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']] = $taxsum + $taxAmt;
                }
//print_r($taxSummArr);die;
                ?>
                @endforeach
                <tr>
                    <td colspan="6" align="right"></td>
                    <td align="center"><!-- {{number_format($sumOfSubtotal, 2)}} --></td>
                    <td><strong>Total</strong></td>
                    <td align="right">{{number_format($sumofPrices, 3)}}</td>
                    <td align="center"><!-- {{number_format($sumOfSubtotal, 2)}} --></td>
                    <td align="right">{{number_format($sumOfTaxAmount, 3)}}</td>
                    <td align="center"></td>
                    <td align="right">{{number_format($sumOfSubtotal, 3)}}</td>
                </tr>
            </table>
            <br>
            <?php
            $taxPerr = '';
            foreach($taxBreakup as $tax1){
                $taxPerr = ($taxPerr=='')?$tax1['tax']:$taxPerr;
            }
            ?>
            <table width="100%" border="0" cellspacing="5" cellpadding="0">
                <tr>
                    <td width="50%" align="left" valign="top">
                        <table cellpadding="5" cellspacing="1" width="100%" class="table table-striped table-bordered table-advance table-hover" style=" word-wrap:break-word; white-space:nowrap;font-size:12px;">
                            <tr class="hedding1 table-headings">
                                <th width="33%" align="center">Tax Type</th>
                                @if($taxPerr!='')
                                <th width="33%" align="center">Tax%</th>
                                @endif
                                <th width="33%" align="center">Tax Amount</th>
                            </tr>
                            @foreach($taxBreakup as $tax)
                            @if($tax['name']!='' && $tax['tax_value']>0)
                            <tr>
                                <td align="center">{{$tax['name']}}</td>
                                @if(isset($tax['tax']) && $tax['tax']!='')
                                <td align="center">{{(float)$tax['tax']}}%</td>
                                @endif
                                <td align="right">{{number_format(($tax['tax_value']), 3)}}</td>
                            </tr>
                            @endif
                            @endforeach
                        </table>	
                    </td>
                    <td width="2%" align="left" valign="top"></td>
                    <td width="48%" align="left" valign="top">
                        <table cellpadding="5" cellspacing="1" width="100%" class="table table-striped table-bordered table-advance table-hover" style=" word-wrap:break-word; white-space:nowrap;font-size:12px;">
                            <tr class="hedding1 table-headings">
                                <th>Total Price</th>
                                <th>Total Tax</th>
                                @if($totDiscount!=0)
                                <th>Total Disc.</th>
                                @endif
                                @if($shipping_fee!=0)
                                <th>Shipping Fee</th>
                                @endif
                                <th>Grand Total</th>
                            </tr>
                            <tr class="odd gradeX">
                                <td align="right">{{number_format($sumofPrices, 3)}}</td>
                                <td align="right">{{number_format($sumOfTaxtotal, 3)}}</td>
                                @if($totDiscount!=0)
                                <td align="right">{{number_format($totDiscount, 3)}}</td>
                                @endif
                                @if($shipping_fee!=0)
                                <td align="right">{{number_format($shipping_fee, 3)}}</td>
                                @endif
                                <td align="right">{{number_format($sumOfGrandtotal, 3)}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>                                          
        </div>
    </body>
</html>
