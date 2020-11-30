<html>
    <head>
    </head>
    <body>
        <table width="100%" border-width = "5">
            <table>
                <tr rowspan="1"><td colspan="12"></td></tr>
                <tr rowspan="1">
                    <td colspan="2"><img src="img/ebutor.png" alt="" height="42" width="42"></td>
                    <td align="left" colspan="5"><strong>{{$loadSheet['leDetail']->business_legal_name}}</strong></td>
                    <td align="" colspan="5"></td>
                </tr>
                <tr rowspan="2"><td colspan="12"></td></tr>
                <tr rowspan="1" style="background-color: #555555;">
                    <th colspan="3">Supplier</th>
                    <th colspan="3">Shipping Address</th>
                    <th colspan="3">Billing Address</th>
                    <th colspan="3">PO Details</th>
                </tr>
                <tr rowspan="1" style="border: 3px solid orange;">
                    <td colspan="3" style="text-align: top;">Name: {{$loadSheet['supplier']->business_legal_name}}<br>
                        Address: {{$loadSheet['supplier']->address1}}, <br/> <?php if ($loadSheet['supplier']->address2 != "") { ?>{{$loadSheet['supplier']->address2}}<br />,<?php } ?>
                        {{$loadSheet['supplier']->city}}, {{$loadSheet['supplier']->state_name}} {{$loadSheet['supplier']->country_name}}, {{$loadSheet['supplier']->pincode}}<br>
                        Phone: {{(isset($loadSheet['userInfo']->mobile_no) ? $loadSheet['userInfo']->mobile_no : '')}}<br>
                        Email: {{(isset($loadSheet['userInfo']->email_id) ? $loadSheet['userInfo']->email_id : '')}}<br>
                        @if(!empty($loadSheet['supplier']->sup_bank_name))
                        Bank Name: {{$loadSheet['supplier']->sup_bank_name}}<br>
                        @endif
                        @if(!empty($loadSheet['supplier']->sup_account_no))
                        A/c No: {{$loadSheet['supplier']->sup_account_no}}<br>
                        @endif
                        @if(!empty($loadSheet['supplier']->sup_account_name))
                        A/c Name: {{$loadSheet['supplier']->sup_account_name}}<br>
                        @endif
                        @if(!empty($loadSheet['supplier']->sup_ifsc_code))
                        IFSC Code: {{$loadSheet['supplier']->sup_ifsc_code}}
                        @endif
                        @if(isset($loadSheet['supplier']->state_name) && !empty($loadSheet['supplier']->state_name))
                        State: {{$loadSheet['supplier']->state_name}}<br>
                        @endif
                        @if(isset($loadSheet['supplier']->state_code) && !empty($loadSheet['supplier']->state_code))
                       State Code: {{$loadSheet['supplier']->state_code}}<br>
                        @endif
                        @if(!empty($loadSheet['supplier']->pan_number))
                        PAN: {{$loadSheet['supplier']->pan_number}}<br>
                        @endif
                        @if(isset($loadSheet['supplier']->gstin) && !empty($loadSheet['supplier']->gstin))
                        GSTIN / UIN: {{$loadSheet['supplier']->gstin}}<br>
                        @endif
                        @if(isset($loadSheet['supplier']->fssai) && !empty($loadSheet['supplier']->fssai))
                        FSSAI NO: {{$loadSheet['supplier']->fssai}}
                        @endif
                    </td>
                    <td colspan="3" style="text-align: top;">@if(!empty($loadSheet['whDetail']->le_wh_code))Code: {{$loadSheet['whDetail']->le_wh_code}}<br>
                        @endif
                        Name: {{$loadSheet['whDetail']->lp_wh_name}}<br>
                        Address: {{$loadSheet['whDetail']->address1}}, <br> <?php if ($loadSheet['whDetail']->address2 != "") { ?>{{$loadSheet['whDetail']->address2}}<br />,<?php } ?>
                        {{$loadSheet['whDetail']->city}}, {{$loadSheet['whDetail']->state_name}}, {{$loadSheet['whDetail']->country_name}} - {{$loadSheet['whDetail']->pincode}}<br>
                        @if(!empty($loadSheet['whDetail']->landmark))
                        Landmark:  {{$loadSheet['whDetail']->landmark}}
                        @endif
                        @if(!empty($loadSheet['whDetail']->contact_name))
                        Contact Person: {{$loadSheet['whDetail']->contact_name}}
                        @endif
                        @if(!empty($loadSheet['whDetail']->phone_no))
                        Phone: {{$loadSheet['whDetail']->phone_no}}<br>
                        @endif
                        @if(!empty($loadSheet['whDetail']->email))
                        Email: {{$loadSheet['whDetail']->email}}<br>
                        @endif                        
                        @if(isset($loadSheet['whDetail']->state_name) && !empty($loadSheet['whDetail']->state_name))
                        State: {{$loadSheet['whDetail']->state_name}}<br>
                        @endif
                        @if(isset($loadSheet['whDetail']->state_code) && !empty($loadSheet['whDetail']->state_code))
                        State Code: {{$loadSheet['whDetail']->state_code}}<br>
                        @endif
                    </td>
                    <td colspan="3" style="word-wrap: normal;">Name: {{$loadSheet['whDetail']->business_legal_name}}<br>
                        Address: {{$loadSheet['whDetail']->address1}}, <br> <?php if ($loadSheet['whDetail']->address2 != "") { ?>{{$loadSheet['whDetail']->address2}}<br />,<?php } ?>
                        {{$loadSheet['whDetail']->city}}, {{$loadSheet['whDetail']->state_name}}, 
                        {{(empty($loadSheet['whDetail']->country_name) ? 'India' : $loadSheet['whDetail']->country_name)}} - {{$loadSheet['whDetail']->pincode}}<br>
                        @if(isset($loadSheet['whDetail']->state_name) && !empty($loadSheet['whDetail']->state_name))
                        State: {{$loadSheet['whDetail']->state_name}}<br>
                        @endif
                        @if(isset($loadSheet['whDetail']->state_code) && !empty($loadSheet['whDetail']->state_code))
                        State Code: {{$loadSheet['whDetail']->state_code}}<br>
                        @endif
                        @if(isset($loadSheet['whDetail']->gstin) && !empty($loadSheet['whDetail']->gstin))
                        GSTIN / UIN: {{$loadSheet['whDetail']->gstin}}<br>
                        @endif
                        @if(isset($loadSheet['whDetail']->fssai) && !empty($loadSheet['whDetail']->fssai))
                        FSSAI NO: {{$loadSheet['whDetail']->fssai}}
                        @endif
                    </td>
                    <td colspan="3" style="word-wrap: normal;"><?php
                        $poType = ($loadSheet['productArr'][0]->po_type == 1 ? 'Qty Based' : 'Value Based');
                        $paymentMode = ($loadSheet['productArr'][0]->payment_mode == 2 ? 'Pre Paid' : 'Post Paid');
                        ?>PO No: {{$loadSheet['productArr'][0]->po_code}}<br>
                        PO Date: {{date('d-m-Y', strtotime($loadSheet['productArr'][0]->po_date))}}<br>
                        Delivery Date: {{date('d-m-Y', strtotime($loadSheet['productArr'][0]->delivery_date))}}<br>
                        PO Type : @if($loadSheet['productArr'][0]->indent_id)
                        Indent- {{$loadSheet['indentCode']}}
                        @else
                        Direct PO ({{$poType}})    
                        @endif
                        <br>
                        Payment Mode: {{$paymentMode}}<br>
                        @if($loadSheet['paymentType'] != '')
                        Payment Type: {{$loadSheet['paymentType']}}<br>
                        @endif
                        @if($loadSheet['productArr'][0]->tlm_name!='')
                        Account: {{$loadSheet['productArr'][0]->tlm_name}}<br>
                        @endif
                        @if($loadSheet['productArr'][0]->payment_refno!='')
                        Payment Ref. No: {{$loadSheet['productArr'][0]->payment_refno}}<br>
                        @endif
                        @if($loadSheet['productArr'][0]->payment_due_date!='' && $loadSheet['productArr'][0]->payment_due_date!='0000-00-00 00:00:00')
                        Payment Due Date: {{date('Y-m-d',strtotime($loadSheet['productArr'][0]->payment_due_date))}}<br>
                        @endif
                        @if($loadSheet['productArr'][0]->logistics_cost!=0)
                        Logistics Cost: {{number_format($loadSheet['productArr'][0]->logistics_cost,2)}}<br>
                        @endif
                        Created By: {{$loadSheet['productArr'][0]->user_name}}<br>
                        Status: {{$loadSheet['poStatus']}}@if($loadSheet['productArr'][0]->is_closed==1)
                        (Debit Note Created)
                        @endif<br>
                        @if($loadSheet['approvedStatus']!='')
                        Approval Status: {{$loadSheet['approvedStatus']}}<br>
                        @endif</td>    
                </tr>
                <tr rowspan="3">
                    <td colspan="12"></td>
                </tr>
                <tr rowspan="1" style="background-color: #555555;">
                    <th colspan="1">S No</th>
                    <th colspan="1">Product Id</th>
                    <th colspan="1">SKU</th> 
                    <th colspan="1">Product Name</th>
                    <th colspan="1">HSN Code</th>
                    <th colspan="1">Qty</th>
                    <th colspan="1">Free Qty</th>
                    <th colspan="1">MRP</th>
                    <th colspan="1">ELP</th>
                    <th colspan="1">Base Rate</th>
                    <th colspan="1">Sub Total</th>
                    <th colspan="1">Tax%</th>
                    <th colspan="1">Tax Amount</th>
                    <th colspan="1">Total</th>
                </tr>

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
                @foreach($loadSheet['productArr'] as $product)
                <?php
                $sumTax = 0;
                $taxText = '';
                ?>


                <?php
                if ($loadSheet['productArr'][0]->po_type == 1) {
                    $taxText = 0;
                }
                $uom = ($product->uom != '' && $product->uom != 0) ? $loadSheet['packTypes'][$product->uom] : 'Ea';
                $free_uom = ($product->free_uom != '' && $product->free_uom != 0) ? $loadSheet['packTypes'][$product->free_uom] : 'Ea';
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
                $newPrClass = (isset($product->newPrClass)) ? $product->newPrClass : '';
                ?>
                <tr rowspan="1" style="border: 3px solid orange;">
                    <td colspan="1">{{$sno++}}</td>
                    <td colspan="1">{{trim($product->product_id)}}</td>
                    <td colspan="1">{{trim($product->sku)}}</td>
                    <td colspan="1"><span {{$newPrClass}}>{{$product->product_title}}</span></td>
                    <td>{{$product->hsn_code}}</td>
                    <td colspan="1">{{$qty}} {{$uom}} {{($uom!='Ea') ? '('.$qty*$no_of_eaches.' Ea)' : ''}}</td>
                    <td colspan="1">{{$free_qty}} {{$free_uom}} {{($free_uom!='Ea') ? '('.$free_qty*$free_no_of_eaches.' Ea)' : ''}}</td>
                    <td colspan="1">{{number_format(($mrp), 3)}}</td>
                    <td colspan="1">{{trim($product->cur_elp)}}</td>
                    <td colspan="1">{{number_format(($basePrice), 3)}}</td>
                    <td colspan="1">{{number_format(($totPrice), 3)}}</td>
                    <td colspan="1">{{empty($product->tax_name) ? '' : $product->tax_name.' @ '}}{{(float)$product->tax_per}}    </td>
                    <td colspan="1">{{number_format($product->tax_amt, 3)}}</td>
                    <td colspan="1">{{number_format(($product->sub_total), 3)}}</td>

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
                <tr rowspan="1" style="border: 3px solid orange;">
                    <td colspan="7"></td>
                    <td colspan="1">Total</td>
                    <td colspan="1">{{number_format($sumofPrices, 3)}}</td>
                    <td colspan="1"></td>
                    <td colspan="1">{{number_format($sumOfTaxAmount, 3)}}</td>                    
                    <td colspan="1">{{number_format($sumOfGrandtotal = ($sumOfSubtotal), 3)}}</td>
                </tr>
                <tr rowspan="3">
                    <td colspan="12"></td>
                </tr>
                 <?php 
                $i = '1'; 
                $taxPerr = '';
                foreach($loadSheet['taxBreakup'] as $tax1){
                    $taxPerr = ($taxPerr=='')?$tax1['tax']:$taxPerr;
                }
            ?>
                <tr rowspan="2" style="background-color: #555555;border: 2px">
                    <th colspan="2">Tax Type</th>
                    @if($taxPerr!='')
                    <th colspan="1">Tax%</th>
                    @endif
                    <th colspan="2">Tax Amt</th>
                    <th colspan="2" style="background-color: #ffffff;border: 2px">       </th>
                    <th colspan="2">Total Price</th>
                    <th colspan="1">Total Tax</th>
                    <th colspan="2">Grand Total</th>
                </tr>               
                @foreach($loadSheet['taxBreakup'] as $tax)
                @if($tax['name']!='' && $tax['tax_value']>0)
                <tr rowspan="3" style="border: 3px solid orange;">
                    <td colspan="2">{{$tax['name']}}</td>
                    @if($taxPerr!='')
                    <td colspan="1">{{(float)$tax['tax']}}%</td>
                    @endif
                    <td colspan="2">
                        {{number_format(($tax['tax_value']), 3)}}</td>
                    <td colspan="2" style="border: 0px white"></td>
                    <?php if ($i == '1') { ?>
                        <td colspan="2">{{number_format($sumofPrices, 3)}}</td>
                        <td colspan="1">{{number_format($sumOfTaxtotal, 3)}}</td>
                        <td colspan="2">{{number_format($sumOfGrandtotal = ($sumOfSubtotal), 3)}}</td>
                        <?php $i = 'o';
                    } else { ?>
                        <td colspan="2"> </td>
                        <td colspan="1"> </td>
                        <td colspan="2"> </td>
                    <?php } ?>
                    @endif
                    @endforeach
                </tr>
            </table>
        </table>
</html>