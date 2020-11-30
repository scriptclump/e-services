<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>
<div class="row">
    <div class="col-md-4">
        <h4>Supplier</h4>
        <div class="well1 margin-top-10">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong> Name </strong></td>
                            <td> {{$supplier->business_legal_name}} </td>
                        </tr>
                        <tr>
                            <td valign="top"><strong> Address </strong></td>
                            <td valign="top"> {{$supplier->address1}}, <br> <?php if ($supplier->address2 != "") { ?>{{$supplier->address2}}<br />,<?php } ?>
                                {{$supplier->city}}, {{$supplier->state_name}}, {{(empty($supplier->country_name) ? 'India' : $supplier->country_name)}},  {{$supplier->pincode}} </td>
                            </td></tr>
                        <tr>
                            <td valign="top"><strong>Phone</strong></td>
                            <td> {{(isset($userInfo->mobile_no) ? $userInfo->mobile_no : '')}} </td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>Email</strong></td>
                            <td> {{(isset($userInfo->email_id) ? $userInfo->email_id : '')}} </td>                        
                        </tr>                        
                        @if(isset($supplier->state_name) && !empty($supplier->state_name))
                        <tr>
                            <td><strong> State </strong></td>
                            <td>{{$supplier->state_name}}</td>
                        </tr>
                        @endif
                        @if(isset($supplier->state_code) && !empty($supplier->state_code))
                        <tr>
                            <td><strong> State Code</strong></td>
                            <td>{{$supplier->state_code}}</td>
                        </tr>
                        @endif
                        @if(!empty($supplier->pan_number))
                        <tr>
                            <td><strong> PAN </strong></td>
                            <td>{{(!empty($supplier->pan_number) ? $supplier->pan_number : 'NA')}}</td>
                        </tr>
                        @endif
                        @if(isset($supplier->gstin) && !empty($supplier->gstin))
                        <tr>
                            <td><strong> GSTIN / UIN </strong></td>
                            <td>{{$supplier->gstin}}</td>
                        </tr>
                        @endif
                        @if(isset($supplier->fssai) && !empty($supplier->fssai))
                        <tr>
                            <td><strong> FSSAI NO </strong></td>
                            <td>{{$supplier->fssai}}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <h4>Delivery Address</h4>
        <div class="well1 margin-top-10">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong> Name </strong></td>
                            <td>{{$whDetail->lp_wh_name}} </td>
                        </tr>
                        <tr>
                            <td valign="top"><strong> Address </strong></td>
                            <td valign="top">{{$whDetail->address1}} <br> <?php if ($whDetail->address2 != "") { ?>{{$whDetail->address2}}<br />,<?php } ?>
                                {{$whDetail->city}}, {{$whDetail->state_name}}, {{$whDetail->country_name}} - {{$whDetail->pincode}} </td>
                        </tr>
                        <tr>
                            <td><strong> Contact Person </strong></td>
                            <td>{{isset($whDetail->contact_name) ? $whDetail->contact_name : 'NA'}}</td>
                        </tr>
                        <tr>
                            <td><strong> Phone </strong></td>
                            <td>{{isset($whDetail->phone_no) ? $whDetail->phone_no : 'NA'}}</td>
                        </tr>
                        <tr>
                            <td><strong> Email </strong></td>
                            <td>{{isset($whDetail->email) ? $whDetail->email : 'NA'}}</td>
                        </tr>                        
                        @if(isset($whDetail->state_name) && !empty($whDetail->state_name))
                        <tr>
                            <td><strong> State </strong></td>
                            <td>{{$whDetail->state_name}}</td>
                        </tr>
                        @endif
                        @if(isset($whDetail->state_code) && !empty($whDetail->state_code))
                        <tr>
                            <td><strong> State Code</strong></td>
                            <td>{{$whDetail->state_code}}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <h4>Invoice Details</h4>
        <div class="well1 margin-top-10">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong> Invoice code </strong></td>
                            <td> {{$productArr[0]->invoice_code}} </td>
                        </tr>
                        <tr>
                            <td><strong> Invoice Date </strong></td>
                            <td>{{Utility::dateFormat($productArr[0]->invoice_date)}}</td>
                        </tr>
                        <tr>
                            <td><strong>GRN code </strong></td>
                            <td><a target="_blank" href="/grn/details/{{$productArr[0]->inward_id}}">{{$productArr[0]->inward_code}}</a></td>
                        </tr>
                        <tr>
                            <td><strong>GRN Date </strong></td>
                            <td>{{Utility::dateFormat($productArr[0]->inward_date)}}</td>
                        </tr>
                        <tr>
                            <td><strong>PO code </strong></td>
                            <td><a href="/po/details/{{$productArr[0]->po_id}}">{{$productArr[0]->po_code}}</a></td>
                        </tr>
                        <tr>
                            <td><strong> Created By </strong></td>
                            <td> {{$productArr[0]->user_name}} </td>
                        </tr>
                        <?php /*<tr>
                            <td><strong> Approval Status </strong></td>
                            <td> {{$approvedStatus}} </td>
                        </tr>*/?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <h4>Product Details</h4>
        <span style="float:right;font-size: 11px; font-weight: bold;">* All Amounts in ({{$curSymbol}}) </span>
        <div class="table-scrollable">
        <table class="table table-striped table-bordered table-advance table-hover table-scrollable" id="sample_2">
            <thead>
                <tr>
                    <th>S&nbsp;No</th>
                    <th>SKU</th>
                    <th>Product&nbsp;Name</th>
                    <th>HSN&nbsp;Code</th>
                    <th style="text-align:right">Qty(Ea)</th>
                    <th style="text-align:right">Free&nbsp;Qty(Ea)</th>
                    <th style="text-align:right">MRP</th>
                    <th style="text-align:right">Unit&nbsp;Base&nbsp;Rate</th>
                    <th style="text-align:right">Sub&nbsp;Total</th>
                    <th style="text-align:right">Tax%</th>
                    <th style="text-align:right">Tax&nbsp;Amount</th>
                    <th style="text-align:right">Discount</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $sno = 1;
                $sumOfSubtotal = 0;
                $sumofPrices = 0;
                $sumOfTaxAmount = 0;
                $sumOfGrandtotal = 0;
                $totQty = 0;
                $taxTypeAmt = array();


                $sumOfQty = 0;
                $sumOfFreeQty = 0;
                $taxper = 0;
                $taxSummArr = array();
                $taxArray = array();
                //print_r($taxBreakup);die;
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
                $totQty = $qty-$free_qty;
                
                $taxAmt = $product->tax_amt;
                $taxName = $product->tax_name;
                $taxPer = $product->tax_per;
                $mrp = $product->mrp;
                $totPrice = $unit_price * $totQty;
                // $taxAmtSum = isset($taxBreakup[$taxPer]['tax_amt'])?$taxBreakup[$taxPer]['tax_amt']:0;
                // $taxBreakup[$taxPer]['tax_name'] = $taxName;
                // $taxBreakup[$taxPer]['tax_amt'] = $taxAmtSum+$taxAmt;
                $newPrClass = (isset($product->newPrClass)) ? $product->newPrClass : '';
                ?>
                <tr class="odd gradeX">
                    <td align="center">{{$sno++}}</td>
                    <td><span {{$newPrClass}}>{{$product->sku}}</span></td>
                    <td><span {{$newPrClass}}>{{$product->product_title}}</span></td>
                    <td>{{$product->hsn_code}}</td>
                    <td align="right">{{$qty}}</td>
                    <td align="right">{{$free_qty}}</td>
                    <td align="right">{{number_format($mrp, 3)}}</td>							
                    <td align="right">
                        {{$unit_price}}
                    </td>
                    <td align="right">{{number_format(($basePrice), 3)}}</td>
                    <td align="right">
                        {{empty($product->tax_name) ? '' : $product->tax_name.' @ '}}{{(float)$product->tax_per}}
                    </td>
                    <td align="right">{{$taxAmt}}</td>
                    <td align="right">{{$product->discount_amount}}</td>
                    <td align="right">{{number_format(($product->sub_total-$product->discount_amount), 3)}}</td>
                </tr>
                <?php
                $sumOfSubtotal = $sumOfSubtotal + ($product->sub_total-$product->discount_amount);
                $totDiscount += $product->discount_amount;
                $sumofPrices +=($totPrice);
                $sumOfTaxAmount += $taxAmt;
                ?>
                @endforeach   
                <?php //echo '<pre/>';print_r($taxBreakup); die;  ?>
                <tr>
                    <td colspan="6" align="right"></td>
                    <td align="center"><!-- {{number_format($sumOfSubtotal, 2)}} --></td>
                    <td><strong>Total</strong></td>
                    <td align="right">{{number_format($sumofPrices, 3)}}</td>
                    <td align="center"><!-- {{number_format($sumOfSubtotal, 2)}} --></td>
                    <td align="right">{{number_format($sumOfTaxAmount, 3)}}</td>
                    <td align="center"></td>
                    <td align="right">{{$curSymbol}}&nbsp;{{number_format($sumOfSubtotal, 3)}}</td>
                </tr>
            </tbody>

        </table>
    </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        @include('PurchaseOrder::Form.approvalForm')
    </div>
    <div class="col-md-4">
        <div class="well">
            <table class="table">
                <?php
                $taxPerr = '';
                foreach($taxBreakup as $tax1){
                    $taxPerr = ($taxPerr=='')?$tax1['tax']:$taxPerr;
                }
                ?>
                <tr>
                    <?php /*<th width="32%">Amt</th>*/ ?>
                    <th width="20%">Tax Type</th>
                    @if($taxPerr!='')
                    <th width="18%">Tax%</th>
                    @endif
                    <th width="30%">Tax Amount</th>
                </tr>
                @foreach($taxBreakup as $tax)
                @if($tax['name']!='' && $tax['tax_value']>0)
                <tr>
                    <?php /*<td>{{number_format($tax['tax_price'],3)}}</td>*/ ?>
                    <td>{{$tax['name']}}</td>
                    @if(isset($tax['tax']) && $tax['tax']!='')
                    <td>{{(float)$tax['tax']}}%</td>
                    @endif
                    <td>{{number_format(($tax['tax_value']), 3)}}</td>
                </tr>
                @endif
                @endforeach
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="well">
            <table class="table">
                <tr>
                    <td width="60%" align="right"><strong>Total Price: </strong></td>
                    <td align="right">{{number_format($sumofPrices, 3)}}</td>
                </tr>
                <tr>
                    <td width="60%" align="right"><strong>Total Tax: </strong></td>
                    <td align="right">{{number_format($sumOfTaxAmount, 3)}}</td>
                </tr>
                @if($totDiscount!=0)
                <tr>
                    <td width="60%" align="right"><strong>Total Disc: </strong></td>
                    <td align="right">{{number_format($totDiscount, 3)}}</td>
                </tr>
                @endif
                @if($shipping_fee!=0)
                <tr>
                    <td width="60%" align="right"><strong>Shipping Fee: </strong></td>
                    <td align="right">{{number_format($shipping_fee, 3)}}</td>
                </tr>
                @endif
                <tr>
                    <td width="60%" align="right"><strong>Grand Total: </strong></td>
                    <td align="right">{{number_format($sumOfGrandtotal, 3)}}</td>
                </tr>

            </table>	

        </div>
    </div>
</div>