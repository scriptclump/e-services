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
                    <th colspan="3">PR Details</th>
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
                       <!--  @if(isset($loadSheet['supplier']->fssai) && !empty($loadSheet['supplier']->fssai))
                        FSSAI NO: {{$loadSheet['supplier']->fssai}}
                        @endif -->
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
                        <!-- @if(isset($loadSheet['whDetail']->fssai) && !empty($loadSheet['whDetail']->fssai))
                        FSSAI NO: {{$loadSheet['whDetail']->fssai}}
                        @endif -->
                    </td>
                    <td colspan="3" style="word-wrap: normal;">PR No: 
                      {{$loadSheet['productArr'][0]->pr_code}}<br>  
                      PR Date: {{date('d-m-Y', strtotime($loadSheet['productArr'][0]->created_at))}}<br>
                      Created By: {{$loadSheet['productArr'][0]->user_name}}<br>
                    </td> 
                </tr>
                <tr rowspan="3">
                    <td colspan="12"></td>
                </tr>
                 <tr rowspan="1" style="background-color: #555555;">
                    <th colspan="1">S No</th>
                    <th colspan="1">SKU</th>
                    <th colspan="1">Product Name</th>
                    <th colspan="1">HSN Code</th> 
                    <th colspan="1">MRP</th>
                    <th colspan="1">Base Rate</th>
                    <th colspan="1">Qty(Ea)</th>
                    <th colspan="1">Taxable Value</th>
                    <th colspan="1">Tax%</th>
                    <th colspan="1">Tax Value</th>
                    <th colspan="1">Total</th>
                </tr>
               <?php
                                            $sno = 1;
                                            $sumOfSubtotal = 0;
                                            $sumOfTaxtotal = 0;
                                            $sumOfTotalValue = 0;
                                            $taxPercentage = 0;
                                            $totQty = 0;
                                            $totdis = 0;
                                            $taxTypeAmt = array();
                                            ?>
                                            @foreach($loadSheet['productArr'] as $product)
                                            <?php
                                            $uom = ($product->uom != '' && $product->uom != 0 && isset($packTypes[$product->uom])) ? $packTypes[$product->uom] : 'Eaches';
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
                                            ?>
                 <tr class="odd gradeX">
                    <td colspan="1">{{$sno++}}</td>
                    <td colspan="1">{{trim($product->sku)}}</td>
                    <td colspan="1">{{$product->product_name}}</td>
                    <td colspan="1">{{$product->hsn_code}}</td>
                    <td colspan="1">{{number_format($product->mrp,2)}}</td>
                    <td colspan="1">{{number_format($basePrice,2)}}</td>
                    <td align="right">{{$qty}} {{$uom}} {{($uom!='Ea') ? '('.$qty*$no_of_eaches.' Ea)' : ''}}
                    </td>                    
                    <td colspan="1">{{number_format($subTotal, 2)}}</td>
                    <td colspan="1">{{empty($product->tax_type) ? '' : $product->tax_type.' @ '}}{{(float)$product->tax_per}}</td>
                    <td colspan="1">{{$product->tax_total}}</td>
                    <td colspan="1">{{number_format($product->total,2)}}</td>
                </tr>
                <?php
                $sumOfTaxtotal = $sumOfTaxtotal + $product->tax_total;
                $sumOfSubtotal = $sumOfSubtotal + $subTotal;
                $sumOfTotalValue = $sumOfTotalValue + $product->total;
                $totQty = $totQty + $product->qty;
                ?>
                @endforeach
                 <tr>
                    <td colspan="7"><strong>Total</strong></td>
                    <td colspan="1">{{number_format($sumOfSubtotal, 2)}}</td>
                    <td colspan="1"></td>
                    <td colspan="1">{{number_format($sumOfTaxtotal, 2)}}</td>
                    <td colspan="1">{{number_format($sumOfTotalValue,2)}}</td>
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
                <th colspan="2">Tax Amount</th>
                <th colspan="2" style="background-color: #ffffff;border: 2px">       </th>
                <th colspan="2">Total Price</th>
                <th colspan="1">Total Tax</th>
                <th colspan="2">Grand Total</th>
            </tr>
            @foreach($loadSheet['taxBreakup'] as $tax)
                @if($tax['name']!='' && $tax['tax_value']>0)
                  <tr rowspan="3" style="border: 3px solid orange;">
                    <td colspan="2">{{$tax['name']}}</td>
                    <td colspan="2">{{number_format(($tax['tax_value']), 3)}}</td>
                   <td colspan="2" style="border: 0px white"></td>
                    <td colspan="2">{{number_format($sumOfSubtotal, 3)}}</td>
                    <td colspan="1">{{number_format($sumOfTaxtotal, 3)}}</td>
                    <td colspan="2">{{number_format($product->pr_grand_total, 3)}}</td>
                    @endif
            @endforeach
            </table>
        </table>
</html>