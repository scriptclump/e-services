<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Purchase Return</title>
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
            .printmartop {margin-top: 10px;}
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
            .thh{ background: #efefef;}
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
                                <td width="10%"><img src="{{$leDetail->logo}}" alt="" height="42" width="42" ></td>
                                @endif
                                <td width="90%"><strong style="padding-top:-20px;"><strong>{{$leDetail->business_legal_name}}</strong></strong></td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%" align="right" valign="middle"></td>
                </tr>
            </table>        

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center"><h4>Purchase Return</h4></td>
                </tr>
            </table>

            <table width="100%" style=" font-size:11px !important; border-collapse:collapse; border-right:1px solid #000;" cellspacing="0" cellpadding="0" border="1">
                <tr>
                    <th width="25%">Supplier</th>
                    <th width="25%">Billing Address</th>
                    <th width="25%">Dispatch Address</th>                        
                    <th width="25%">PR Details</th>
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
                            <strong>GSTIN / UIN:</strong> @if(isset($supplier->gstin) && !empty($supplier->gstin)){{$supplier->gstin}}@endif
                        </td>
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
                            <strong>GSTIN / UIN:</strong> @if(isset($supplier->gstin) && !empty($supplier->gstin)){{$supplier->gstin}}@endif
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
                            <strong>GSTIN / UIN:</strong> @if(isset($leDetail->gstin) && !empty($leDetail->gstin)){{$leDetail->gstin}}@endif
                        </td>                        
                        <td align="left" valign="top">
                            <strong>PR No: </strong> {{$productArr[0]->pr_code}}<br/>
                            <strong>PR Date: </strong> {{Utility::dateFormat($productArr[0]->created_at)}}<br/>
                            <strong>Created By: </strong> {{$productArr[0]->user_name}}<br/>
                        </td>
                </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="right"<span style="float:right;font-size: 10px;font-weight: bold;">* All Amounts in (Rs.) </span></td>
                </tr>
            </table>
            <table width="100%" style=" font-size:11px !important; border-collapse:collapse; border-right:1px solid #000; margin-top:10px" cellspacing="0" cellpadding="5" border="1">
                <thead>
                <tr class="thh">
                    <th rowspan="2" align="center">S&nbsp;No</th>
                    <th rowspan="2" align="left">Product&nbsp;Name</th>
                    <th rowspan="2" align="left">HSN<br>Code</th>
                    <th rowspan="2" align="right">MRP</th>
                    <th rowspan="2" align="right">Rate</th>
                    <th rowspan="2" align="right">Qty(Ea)</th>
                    <th rowspan="2" align="right">Taxable<br>Value</th>
                    <th rowspan="2" align="right">Tax%</th>
                    <th rowspan="2" align="right">Tax<br>Value</th>
                    <th colspan="2" align="center">CGST</th>
                    <th colspan="2" align="center">SGST/UTGST</th>
                    <th colspan="2" align="center">IGST</th>
                    <th rowspan="2" align="right">Total</th>    
                </tr>
                <tr>
                    <th colspan="1" rowspan="1" class="thh">%</th>
                    <th colspan="1" rowspan="1" class="thh">Amt</th>
                    <th colspan="1" rowspan="1" class="thh">%</th>
                    <th colspan="1" rowspan="1" class="thh">Amt</th>
                    <th colspan="1" rowspan="1" class="thh">%</th>
                    <th colspan="1" rowspan="1"><span class="thh">Amt</span></th>
                </tr>
            </thead>
                <?php
                $sno = 1;
                $sumOfSubtotal = 0;
                $sumOfTaxtotal = 0;
                $sumOfTotalValue = 0;
                $taxPercentage = 0;
                $totQty = 0;
                $totdis = 0;
                $taxTypeAmt = array();
                $totCGST = $totSGST = $totIGST = $totUTGST = 0;
                ?>
                @foreach($productArr as $product)
                <?php
                $uom = ($product->uom != '' && $product->uom != 0 && isset($packTypes[$product->uom])) ? $packTypes[$product->uom] : 'Ea';
                $free_uom = ($product->free_uom != '' && $product->free_uom != 0 && isset($packTypes[$product->free_uom])) ? $packTypes[$product->free_uom] : 'Eaches';
                $soh_qty = ($product->qty != '') ? $product->qty : 0;
                $dit_qty = ($product->dit_qty != '') ? $product->dit_qty : 0;
                $dnd_qty = ($product->dnd_qty != '') ? $product->dnd_qty : 0;
                $qty = $soh_qty + $dit_qty + $dnd_qty;
                $free_qty = ($product->free_qty != '') ? $product->free_qty : 0;
                $no_of_eaches = ($product->no_of_eaches == 0 || $product->no_of_eaches == '') ? 1 : $product->no_of_eaches;
                $free_no_of_eaches = ($product->free_eaches == 0 || $product->free_eaches == '') ? 1 : $product->free_eaches;

                $isTaxInclude = $product->is_tax_included;
                $basePrice = $product->price;
                $subTotal = $product->sub_total;
                if ($isTaxInclude == 1) {
                    $basePrice = ($basePrice / (1 + ($product->tax_per / 100)));
                    $subTotal = ($subTotal / (1 + ($product->tax_per / 100)));
                }
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

                $cgst_val = ($product->tax_total * $cgst) / 100;
                $sgst_val = ($product->tax_total * $sgst) / 100;
                $igst_val = ($product->tax_total * $igst) / 100;
                $utgst_val = ($product->tax_total * $utgst) / 100;
                // }
                $totCGST = $totCGST + $cgst_val;
                $totSGST = $totSGST + $sgst_val;
                $totIGST = $totIGST + $igst_val;
                $totUTGST = $totUTGST + $utgst_val;
                ?>
                <tr class="odd gradeX">
                    <td align="left" valign="middle">{{$sno++}}</td>
                    <td align="left" valign="middle">{{$product->product_name}}</td>
                    <td align="left" valign="middle">{{$product->hsn_code}}</td>
                    <td align="left" valign="middle">{{$product->mrp}}</td>
                    <td align="right" valign="middle">{{number_format($basePrice,2)}}</td>
                    <td align="right">{{$qty}}<br/>
                      <?php /* <span style="font-size:10px;font-weight: bold;">
                            @if($soh_qty >0)SOH Qty: {{$soh_qty}}<br> @endif
                            @if($dit_qty >0)DIT Qty: {{$dit_qty}}<br> @endif
                            @if($dnd_qty >0)DND Qty: {{$dnd_qty}}@endif
                        </span> */ ?>
                    </td>                    
                    <td align="right" valign="middle">{{number_format($subTotal, 2)}}</td>
                    <td align="right">{{empty($product->tax_type) ? '' : $product->tax_type.' @ '}}{{(float)$product->tax_per}}</td>
                    <td align="right">{{$product->tax_total}}</td>
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
                    <td align="right" valign="middle">{{number_format($product->total,2)}}</td>
                </tr>
                <?php
                $sumOfTaxtotal = $sumOfTaxtotal + $product->tax_total;
                $sumOfSubtotal = $sumOfSubtotal + $subTotal;
                $sumOfTotalValue = $sumOfTotalValue + $product->total;
                $totQty = $totQty + $product->qty;
                //$totdis = $totdis + $product->discount_amt;
                ?>
                @endforeach

                <tr>
                    <td colspan="6" align="right" valign="middle"><strong>Total</strong></td>
                    <td align="right"><strong>{{number_format($sumOfSubtotal, 2)}}</strong></td>                            
                    <td align="right"></td>
                    <td align="right"><strong>{{number_format($sumOfTaxtotal, 2)}}</strong></td>
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
                    <td align="right"><strong>{{number_format($sumOfTotalValue,2)}}</strong></td>
                </tr>
            </table>
            <br>
            <table width="100%" border="0" cellspacing="5" cellpadding="0">
                <tr>
                    <td width="50%" align="left" valign="top">
                        <table cellpadding="2" cellspacing="2" class="table" width="100%">
                            <tr>
                                <td style="word-wrap:break-word;font-size:12px;width:100%;font-weight:bold;">Grand Total In Words: <?php echo Utility::convertNumberToWords(round($sumOfTotalValue,2)); ?></td>
                            </tr>
                            <tr>
                                <td style="word-wrap:break-word;font-size:11px;width:100%;">* Reverse Charges not Applicable</td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%" align="left" valign="top">
                                                
                    </td>
                </tr>
            </table>
            <br>
            @if(!empty($productArr[0]->pr_remarks))
            <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" bgcolor="#efefef" style="margin-top:30px;">
                <tr>
                    <td align="left" valign="middle" style="padding:10px;"><strong style="font-size:12px;">Remarks:</strong> {{$productArr[0]->pr_remarks}}</td>
                </tr>
            </table>
            @endif
        </div>
    </body>
</html>
