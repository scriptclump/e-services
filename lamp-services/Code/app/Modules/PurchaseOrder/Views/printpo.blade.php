<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>
<html dir="ltr" lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Purchase Order</title>
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
                                <td><img src="{{$leDetail->logo}}" alt="Logo"></td>
                                @endif
                                <td><strong style="padding-top:-20px;">{{$leDetail->business_legal_name}}</strong></td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%" align="right" valign="middle"></td>
                </tr>
            </table>
            <div style="margin:0px auto; text-align:center;">
                <h4>Purchase Order</h4>
            </div> 
            <table width="100%" class="table table-bordered thline printtable " cellpadding="5" style=" word-wrap:break-word;font-size:13px;">
                <tr style="font-size:16px; text-align:left" class="hedding1 table-headings">
                    <th width="25%">Supplier</th>
                    <th width="25%">Shipping Address</th>
                    <th width="25%">Billing Address</th>
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
                        @if(isset($supplier->state_name) && !empty($supplier->state_name))
                        <strong>State:</strong> {{$supplier->state_name}}<br>
                        @endif
                        @if(isset($supplier->state_code) && !empty($supplier->state_code))
                        <strong>State Code:</strong> {{$supplier->state_code}}<br>
                        @endif
                        @if(!empty($supplier->pan_number))
                        <strong>PAN:</strong> {{$supplier->pan_number}}<br>
                        @endif
                        @if(isset($supplier->gstin) && !empty($supplier->gstin))
                        <strong>GSTIN / UIN:</strong> {{$supplier->gstin}}<br>
                        @endif
                        @if(isset($supplier->fssai) && !empty($supplier->fssai))
                        <strong>FSSAI NO:</strong> {{$supplier->fssai}}
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
                        @if(isset($whDetail->state_name) && !empty($whDetail->state_name))
                        <strong>State:</strong> {{$whDetail->state_name}}<br>
                        @endif
                        @if(isset($whDetail->state_code) && !empty($whDetail->state_code))
                        <strong>State Code:</strong> {{$whDetail->state_code}}<br>
                        @endif
                    </td>                    
                    <td valign="top" style="font-size:15px;">
                        <strong>Name:</strong> {{$whDetail->business_legal_name}}<br>
                        <strong>Address:</strong> {{$whDetail->address1}}, <br> <?php if ($whDetail->address2 != "") { ?>{{$whDetail->address2}}<br />,<?php } ?>
                        {{$whDetail->city}}, {{$whDetail->state_name}}, {{(empty($whDetail->country_name) ? 'India' : $whDetail->country_name)}} - {{$whDetail->pincode}}<br>
                        @if(isset($whDetail->state_name) && !empty($whDetail->state_name))
                        <strong>State:</strong> {{$whDetail->state_name}}<br>
                        @endif
                        @if(isset($whDetail->state_code) && !empty($whDetail->state_code))
                        <strong>State Code:</strong> {{$whDetail->state_code}}<br>
                        @endif
                        @if(isset($whDetail->gstin) && !empty($whDetail->gstin))
                        <strong>GSTIN / UIN:</strong> {{$whDetail->gstin}}<br>
                        @endif
                        @if(isset($whDetail->fssai) && !empty($whDetail->fssai))
                        <strong>FSSAI NO:</strong> {{$whDetail->fssai}}
                        @endif
                    </td>
                    <td valign="top" style="font-size:15px;">
                        <?php 
                            $poType = ($productArr[0]->po_type == 1 ? 'Qty Based' : 'Value Based');
                            $paymentMode = ($productArr[0]->payment_mode == 2 ? 'Pre Paid' : 'Post Paid');
                        ?>
                        <strong>PO No:</strong> {{$productArr[0]->po_code}}<br>
                        <strong>PO Date:</strong> {{Utility::dateFormat($productArr[0]->po_date)}}<br>
                        <strong>Delivery Date:</strong> {{Utility::dateFormat($productArr[0]->delivery_date))}}<br>
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
            <span style="float:right;font-size: 11px;font-weight: bold;">* All Amounts in ({{$curSymbol}}) </span>
            <table cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-advance table-hover" style="word-wrap:break-word;white-space:nowrap;font-size:14px;">
                <thead>
                <tr class="hedding1 table-headings">
                    <th height="30">S&nbsp;No</th>
                    <th>SKU</th>
                    <th>Product&nbsp;Name</th>
                    <th>HSN&nbsp;Code</th>
                    <th>Qty</th>
                    <th>Free&nbsp;Qty</th>
                    <th>MRP</th>
                    <th>Base&nbsp;Rate</th>
                    <th>Taxable&nbsp;Value</th>
                    <th>Tax%</th>
                    <th>Tax&nbsp;Amount</th>
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
                ?>
                @foreach($productArr as $product)
                <?php
                $sumTax = 0;
                $taxText = '';
                ?>


                <?php
                if ($productArr[0]->po_type == 1) {
                    $taxText = 0;
                }
                $uom = ($product->uom!=''&&$product->uom!=0) ? $packTypes[$product->uom] : 'Ea';
                $free_uom = ($product->free_uom != '' && $product->free_uom != 0) ? $packTypes[$product->free_uom] : 'Ea';
                $qty = ($product->qty != '') ? $product->qty : 0;
                $free_qty = ($product->free_qty != '') ? $product->free_qty : 0;
                $no_of_eaches = ($product->no_of_eaches == 0 || $product->no_of_eaches == '') ? 1 : $product->no_of_eaches;
                $free_no_of_eaches = ($product->free_eaches == 0 || $product->free_eaches == '') ? 1 : $product->free_eaches;
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
                $newPrClass = (isset($product->newPrClass))?$product->newPrClass:'';
                ?>
               <tbody>
                <tr class="odd gradeX">
                    <td align="center">{{$sno++}}</td>
                    <td><span {{$newPrClass}}>{{$product->sku}}</span></td>
                    <td style="white-space:normal;"><span {{$newPrClass}}>{{$product->product_title}}</span></td>
                    <td>{{$product->hsn_code}}</td>
                    <td>{{$qty}} {{$uom}} {{($uom!='Ea') ? '('.$qty*$no_of_eaches.' Ea)' : ''}}</td>
                    <td>{{$free_qty}} {{$free_uom}} {{($free_uom!='Ea') ? '('.$free_qty*$free_no_of_eaches.' Ea)' : ''}}</td>
                    <td align="right">{{number_format($product->mrp, 3)}}</td>
                    <td align="right">{{number_format(($basePrice), 3)}}</td>
                    <td align="right">{{number_format(($totPrice), 3)}}</td>
                    <td align="center">{{empty($product->tax_name) ? '' : $product->tax_name.' @ '}}{{(float)$product->tax_per}}    </td>
                    <td align="right">{{number_format($product->tax_amt, 3)}}</td>
                    <td align="right">{{number_format(($product->sub_total), 3)}}</td>
                </tr>
                <?php
                $sumOfTaxtotal = $sumOfTaxtotal + $product->tax_amt;
                $sumOfSubtotal = $sumOfSubtotal + $product->sub_total;
                $sumofPrices +=($totPrice);
                $sumOfTaxAmount += $product->tax_amt;
                if (isset($tax['Tax Type']) && $tax['Tax Percentage']) {
                    $taxsum = isset($taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']]) ? $taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']] : 0;
                    $taxSummArr[$tax['Tax Type']][(string) $tax['Tax Percentage']] = $taxsum + $taxAmt;
                }
                ?>
                @endforeach
                <tr>
                    <td colspan="6" align="right"></td>
                    <td align="center"><!-- {{number_format($sumOfSubtotal, 2)}} --></td>
                    <td><strong>Total</strong></td>
                    <td align="right">{{number_format($sumofPrices, 3)}}</td>
                    <td align="center"><!-- {{number_format($sumOfSubtotal, 2)}} --></td>
                    <td align="right">{{number_format($sumOfTaxAmount, 3)}}</td>
                    <td align="right">{{number_format($sumOfGrandtotal = ($sumOfSubtotal), 3)}}</td>
                </tr>
            </tbody>
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
                                <th width="33%" align="center">Total Price</th>
                                <th width="33%" align="center">Total Tax</th>
                                <th width="33%" align="center">Grand Total</th>
                            </tr>
                            <tr class="odd gradeX">
                            <!-- <td>{{$product->symbol}} {{number_format($sumOfSubtotal, 2)}}</td> -->
                                <td align="right">{{number_format($sumofPrices, 3)}}</td>
                                <td align="right">{{number_format($sumOfTaxtotal, 3)}}</td>
                                <td align="right">{{number_format($sumOfGrandtotal = ($sumOfSubtotal), 3)}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            @if(!empty($productArr[0]->po_remarks))
            <table width="100%" border="0" align="left" cellpadding="5" cellspacing="0">
                <tr>
                    <td align="left" valign="middle"><strong style="font-size:12px;">Remarks:</strong></td>
                </tr>
                <tr>
                    <td align="left" valign="middle" style="font-size:12px;">{{$productArr[0]->po_remarks}}</td>
                </tr>

            </table>

            @endif                                           
        </div>
    </body>
</html>
