<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>

<div class="row">
    <div class="col-md-4 col-sm-4">
        <h4>Supplier</h4>
        <div class="well1 margin-top-10 padding-1">
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
                        @if(!empty($supplier->sup_bank_name))
                        <tr>
                            <td><strong> Bank Name </strong></td>
                            <td>{{(!empty($supplier->sup_bank_name) ? $supplier->sup_bank_name : 'NA')}}</td>
                        </tr>
                        @endif
                        @if(!empty($supplier->sup_account_no))
                        <tr>
                            <td><strong> A/c No </strong></td>
                            <td>{{(!empty($supplier->sup_account_no) ? $supplier->sup_account_no : 'NA')}}</td>
                        </tr>
                        @endif
                        @if(!empty($supplier->sup_account_name))
                        <tr>
                            <td><strong> A/c Name </strong></td>
                            <td>{{(!empty($supplier->sup_account_name) ? $supplier->sup_account_name : 'NA')}}</td>
                        </tr>
                        @endif
                        @if(!empty($supplier->sup_ifsc_code))
                        <tr>
                            <td><strong> IFSC Code </strong></td>
                            <td>{{(!empty($supplier->sup_ifsc_code) ? $supplier->sup_ifsc_code : 'NA')}}</td>
                        </tr>
                        @endif
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

    <div class="col-md-4 col-sm-4">
        <h4>Delivery Address</h4>
        <div class="well1 margin-top-10 padding-1">
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
                            <td><strong> Contact&nbsp;Person </strong></td>
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

    <div class="col-md-4 col-sm-4">
        <h4>PO Details</h4>
        <div class="well1 margin-top-10 padding-1">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong> PO Number </strong></td>
                            <td> {{$productArr[0]->po_code}} </td>
                        </tr>
                        <tr>
                            <td><strong> PO Date </strong></td>
                            <td>{{Utility::dateTimeFormat($productArr[0]->po_date)}}, <strong>Del. Date:</strong> {{Utility::dateFormat($productArr[0]->delivery_date)}}</td>
                        </tr>
                        @if(!empty($productArr[0]->exp_delivery_date) && $productArr[0]->exp_delivery_date != '0000-00-00')
                        <tr>
                            <td><strong> Expected Delivery Date </strong></td>
                            <td>{{Utility::dateFormat($productArr[0]->exp_delivery_date)}}</td>
                        </tr>
                        @endif
                        <?php 
                            $poType = ($productArr[0]->po_type == 1 ? 'Qty Based' : 'Value Based'); 
                            $paymentMode = ($productArr[0]->payment_mode == 2 ? 'Pre Paid' : 'Post Paid'); 
                        ?>
                        <tr>
                            <td><strong> PO Type </strong></td>
                            <td> {{(!empty($productArr[0]->indent_id) ? 'Indent-'.$indentCode : "Direct PO ($poType)")}} </td>
                        </tr>
                        <tr>
                            <td><strong> Payment Mode </strong></td>
                            <td> {{$paymentMode}}@if($paymentType != ''), <strong>Type:</strong> {{$paymentType}}@endif</td>
                        </tr>
                        @if($productArr[0]->tlm_name!='')
                        <tr>
                            <td><strong> Account </strong></td>
                            <td>{{$productArr[0]->tlm_name}}</td>
                        </tr>
                        @endif
                        @if($productArr[0]->payment_refno!='')
                        <tr>
                            <td><strong> Payment Ref. No</strong></td>
                            <td>{{$productArr[0]->payment_refno}}</td>
                        </tr>
                        @endif
                        @if($productArr[0]->payment_due_date!='' && $productArr[0]->payment_due_date!='0000-00-00 00:00:00')
                        <tr>
                            <td><strong> Payment Due Date </strong></td>
                            <td>{{Utility::dateFormat($productArr[0]->payment_due_date)}}</td>
                        </tr>
                        @endif
                        @if($productArr[0]->logistics_cost!=0)
                        <tr>
                            <td><strong> Logistics Cost </strong></td>
                            <td> {{number_format($productArr[0]->logistics_cost,2)}} </td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong> Created By </strong></td>
                            <td> {{$productArr[0]->user_name}} </td>
                        </tr>
                        @if($productArr[0]->dc_name!="")
                        <tr>
                            <td><strong> DC to Supply </strong></td>
                            <td> {{$productArr[0]->dc_name}} </td>
                        </tr>
                        @endif
                        @if($productArr[0]->st_dc_name!="")
                        <tr>
                            <td><strong> Stock Tranfer From </strong></td>
                            <td> {{$productArr[0]->st_dc_name}} </td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong> Status </strong></td>
                            <td>{{$poStatus}}
                                @if($productArr[0]->is_closed==1)
                                (Debit Note Created)
                                @endif
                            </td>
                        </tr>
                        @if($approvedStatus!='')
                        <tr>
                            <td><strong> Approval Status </strong></td>
                            <td> {{$approvedStatus}} </td>
                        </tr>
                        @endif
                        @if($productArr[0]->paymentStatus!='')
                        <tr>
                            <td><strong> Payment Status </strong></td>
                            <td> {{$productArr[0]->paymentStatus}} </td>
                        </tr>
                        @endif
                        @if(isset($poDocs) && count($poDocs)>0)
                        <tr>
                            <td><strong> Proforma Invoice(Quote) </strong></td>
                            <td>
                              <div class="downloaddocs">
                                @foreach($poDocs as $docs)    
                                    <?php
                                  $var = pathinfo($docs->file_path,PATHINFO_EXTENSION);
                                  ?>
                                  @if($var == 'jpg' || $var == 'jpeg' || $var == 'png')
                                    <span data-lightbox="property">
                                        <img src="{{$docs->file_path}}" data-url="{{$docs->file_path}}" class="img img-test img-sizes" style="width: 25px;height: 25px;cursor: pointer;"/>
                                    </span>
                                    @else
                                    <a href="{{$docs->file_path}}" class="closedownload">
                                        <i class="fa fa-download"></i>
                                    </a>
                                   @endif
                                @endforeach
                              </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="overflow-x:auto;">
        <h4>Product Details</h4>
        <span style="float:right;font-size: 11px; font-weight: bold;">* All Amounts in ({{$curSymbol}}) </span>
        <table class="table table-striped table-bordered table-advance table-hover table-scrolling" id="sample_2-pd">
            <thead>
                <tr>
                    <th>S&nbsp;No</th>
                    <th>Product&nbsp;Name</th>
                    <th>HSN&nbsp;Code</th>
                    <th>Qty</th>
                    <th>Free&nbsp;Qty</th>
                    <th style="text-align:right" title="Available inventory days">Inv&nbsp;Days</th>
                    <th style="text-align:right">MRP</th>
                    <th style="text-align:right" title="Current {{Lang::get('headings.LP')}}">{{Lang::get('headings.LP')}}</th>

                    @if($is_Supplier == 0)
                        <th style="text-align:right" title="Previous {{Lang::get('headings.LP')}} across all suppliers & dc">Previous&nbsp;{{Lang::get('headings.LP')}}</th>
                        <th style="text-align:right" title="Past 30 Days Lowest {{Lang::get('headings.LP')}}">30D</th>
                        <th style="text-align:right" title="Start to Date Lowest {{Lang::get('headings.LP')}}">STD</th>
                    @endif
                   <?php /* <th style="text-align:right" title="Supplier Landing Price">SLP</th> */?>
                    <th style="text-align:right">Base&nbsp;Rate</th>
                    <th style="text-align:right">Taxable&nbsp;Value</th>
                   <?php /* <th>Tax%</th> */ ?>
                    <th style="text-align:right">Tax&nbsp;Amount</th>
                    <th>Discount</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $sno = 1;
                $sumOfSubtotal = 0;
                $sumofPrices = 0;
                $sumOfTaxtotal = 0;
                $sumOfTaxAmount = 0;
                $sumOfGrandtotal = 0;
                $totQty = 0;
                $taxTypeAmt = array();


                $sumOfQty = 0;
                $sumOfFreeQty = 0;
                $taxper = 0;
                $taxSummArr = array();
                $taxArray = array();
                //echo '<pre/>';print_r($productArr);die;
                $grandTotal = 0;
                foreach($productArr as $product1){
                    $discAmtItem = 0;
                    if($product1->apply_discount==1){
                        if($product1->item_discount_type==1){
                            $discAmtItem = ($product1->sub_total*$product1->item_discount)/100;
                        }else{
                            $discAmtItem = $product1->item_discount;
                        }
                    }
                    $grandTotal += ($product1->sub_total-$discAmtItem);
                }
                $discount_before_tax = (isset($productArr[0]->discount_before_tax) && $productArr[0]->discount_before_tax == 1)?$productArr[0]->discount_before_tax:0;
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
                $uom = ($product->uom != '' && $product->uom != 0 && isset($packTypes[$product->uom])) ? $packTypes[$product->uom] : 'Ea';
                $free_uom = ($product->free_uom != '' && $product->free_uom != 0 && isset($packTypes[$product->free_uom])) ? $packTypes[$product->free_uom] : 'Eaches';
                $qty = ($product->qty != '') ? $product->qty : 0;
                $free_qty = ($product->free_qty != '') ? $product->free_qty : 0;
                $no_of_eaches = ($product->no_of_eaches == 0 || $product->no_of_eaches == '') ? 1 : $product->no_of_eaches;
                $free_no_of_eaches = ($product->free_eaches == 0 || $product->free_eaches == '') ? 1 : $product->free_eaches;
                $basePrice = $product->price;
                $isTaxInclude = $product->is_tax_included;
                $unit_price = $product->unit_price;
                $sub_total = $product->sub_total;
                $discAmt = 0;
                $current_elp = $product->unit_price;
                $totQty = (($qty * $no_of_eaches) - ($free_qty * $free_no_of_eaches));
                if ($isTaxInclude == 1) {
                    $basePrice = ($basePrice / (1 + ($product->tax_per / 100)));
                    $unit_price = ($unit_price / (1 + ($product->tax_per / 100)));
                }else{
                    $current_elp = $unit_price+(($unit_price*$product->tax_per)/100);
                }                
                if($product->apply_discount==1){
                    if($product->item_discount_type==1){
                        $discAmt = ($sub_total*$product->item_discount)/100;
                    }else{
                        $discAmt = $product->item_discount;
                    }
                }
                $totalAfteritemDisc = $sub_total-$discAmt;
                if($product->apply_discount_on_bill==1){
                    if($product->discount_type==1){
                        $discAmt = $discAmt+($totalAfteritemDisc*$product->discount)/100;
                    }else{
                        $contribution = ($grandTotal>0)?($totalAfteritemDisc/$grandTotal):0;
                        $discAmt = $discAmt+($product->discount*$contribution);
                    }
                }
                $unit_disc = $discAmt/($qty * $no_of_eaches);
                $current_elp = ($current_elp-$unit_disc); // curelp is recalculating to make old data correct which dont have cur_elp
                if($discount_before_tax==1){
                    $current_elp = $product->cur_elp;
                }
                // rajradclief color changes
                $current_elp_colour = number_format($current_elp,3);
                $prev_elp_colour = number_format($product->prev_elp,3);
                if ($current_elp_colour>$prev_elp_colour) {
                    $css_colour = "style='color:red'";
                }else{
                    $css_colour='';
                    }
                $taxAmt = $product->tax_amt;
                $taxName = $product->tax_name;
                $taxPer = $product->tax_per;
                $mrp = $product->mrp;
                $totPrice = $unit_price * $totQty;
                $newPrClass = (isset($product->newPrClass)) ? $product->newPrClass : '';
                $thirtyd = (isset($product->thirtyd) && $product->thirtyd>0)?$product->thirtyd:$product->dlp;
                ?>
                <tr class="odd gradeX">
                    <td align="center">{{$sno++}}</td>
                    <td><span {{$newPrClass}}>{{$product->product_title}}</span><br/><span style="font-size: 11px;font-weight: bold;">SKU - {{$product->sku}}</span></td>
                    <td>{{$product->hsn_code}}</td>
                    <td>{{$qty}} {{$uom}} {{($uom!='Ea') ? '('.$qty*$no_of_eaches.' Eaches)' : ''}}</td>
                    <td>{{$free_qty}} {{$free_uom}} {{($free_uom!='Eaches') ? '('.$free_qty*$free_no_of_eaches.' Eaches)' : ''}}</td>
                    <td align="right">{{ceil($product->avlble_inv_days)}}</td>
                    <td align="right">{{number_format($mrp, 3)}}</td>
                    <!-- <td align="right">{{number_format($current_elp, 3)}}</td> -->
                    <td align="right" <?php echo $css_colour?> >{{number_format($current_elp, 3)}}</td>
                    @if($is_Supplier == 0)
                        <td align="right">{{number_format($product->prev_elp, 3)}}</td>
                        <td align="right">{{number_format($thirtyd, 3)}}</td>
                        <td align="right">{{number_format($product->std, 3)}}</td>
                    @endif                    
                    <?php /*<td align="right">{{number_format($product->slp, 3)}}</td> */?>
                    <td align="right">
                        {{number_format(($basePrice), 3)}}
                    </td>
                    <td align="right">{{number_format(($totPrice), 3)}}</td>
                   <?php /* <td align="right">
                        {{empty($product->tax_name) ? '' : $product->tax_name.' @ '}}{{(float)$product->tax_per}}
                    </td>*/ ?>
                    <td align="right">{{$product->tax_amt}}<br/><span style="font-size: 11px; font-weight: bold;">{{empty($product->tax_name) ? '' : $product->tax_name.' @ '}}{{(float)$product->tax_per}}</span></td>
                    <td align="left">
                        Apply: <?php echo ($product->apply_discount==1)?'Yes':'No'; ?>
                        Value: <?php echo ($product->item_discount!='')?number_format($product->item_discount,2):0; echo ($product->item_discount_type==1)?'%':' Flat'; ?>
                    </td>
                    <td align="right">{{number_format(($product->sub_total), 3)}}</td>
                </tr>
                <?php
                $sumOfTaxtotal = $sumOfTaxtotal + $taxAmt;
                $sumOfSubtotal = $sumOfSubtotal + $product->sub_total;
                $sumofPrices +=($totPrice);
                $sumOfQty = $sumOfQty + $product->qty;
                $sumOfFreeQty = $sumOfFreeQty + $free_qty;
                $sumOfTaxAmount += $product->tax_amt;
                ?>
                @endforeach   
                <?php //echo '<pre/>';print_r($taxBreakup); die;  ?>
                <tr>
                    <td colspan=<?php if(isset($is_Supplier) && $is_Supplier == 0) echo '10'; else echo '7'; ?> align="right"></td>
                    <td align="center"><!-- {{number_format($sumOfSubtotal, 2)}} --></td>
                    <td><strong>Total</strong></td>
                    <td align="right">{{number_format($sumofPrices, 3)}}</td>
                    <td align="right">{{number_format($sumOfTaxAmount, 3)}}</td>
                    <td align="center"><!-- {{number_format($sumOfSubtotal, 2)}} --></td>
                    <td align="right">{{number_format($sumOfGrandtotal = ($sumOfSubtotal), 3)}}</td>
                </tr>
            </tbody>

        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-sm-6 hidden-sm">
        <label><strong>Discount On Bill</strong></label>
        Apply: <?php echo ($product->apply_discount_on_bill==1)?'Yes':'No'; ?>,
        Value: <?php echo ($product->discount!='')?number_format($product->discount,2):0; echo ($product->discount_type==1)?'%':' Flat'; ?>,
        Applicable Before Tax: <?php echo ($product->discount_before_tax==1)?'Yes':'No'; ?>
        Stock Transfer: <?php echo ($product->stock_transfer==1)?'Yes':'No'; ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4 col-sm-6 hidden-sm">
    @if($productArr[0]->po_status!="87003" && $productArr[0]->po_status!="87004")
        @if(in_array($productArr[0]->approval_status,[57117]) && $productArr[0]->po_status=="87001")
        @else
        @include('PurchaseOrder::Form.approvalForm')
        @endif 
    @endif
    </div>
    <?php
    $taxPerr = '';
    foreach($taxBreakup as $tax1){
        $taxPerr = ($taxPerr=='')?$tax1['tax']:$taxPerr;
    }
    ?>
    <div class="col-md-4 col-sm-6">
        <div class="well tax-typ-amt">
            <table class="table">
                <tr>
                    <th width="33%" align="center">Tax Type</th>
                    @if($taxPerr!='')
                    <th width="33%" align="center">Tax%</th>
                    @endif
                    <th width="33%" align="center">Tax Amount</th>
                </tr>
                @foreach($taxBreakup as $tax)
                @if($tax['name']!='' && $tax['tax_value']>0)
                <tr>
                    <td align="left">{{$tax['name']}}</td>
                    @if(isset($tax['tax']) && $tax['tax']!='')
                    <td align="left">{{(float)$tax['tax']}}%</td>
                    @endif
                    <td align="left">{{number_format(($tax['tax_value']), 3)}}</td>
                </tr>
                @endif
                @endforeach
            </table>	
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="well tax-typ-amt">
            <table class="table">
                <tr>
                    <td width="60%" align="right"><strong>Total Price: </strong></td>
                    <td align="right">{{number_format($sumofPrices, 3)}}</td>
                </tr>
                <tr>
                    <td width="60%" align="right"><strong>Total Tax: </strong></td>
                    <td align="right">{{number_format($sumOfTaxtotal, 3)}}</td>
                </tr>
                <tr>
                    <td width="60%" align="right"><strong>Grand Total: </strong></td>
                    <td align="right">{{number_format($sumOfGrandtotal = ($sumOfSubtotal), 3)}}</td>
                </tr>

            </table>	

        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <h4>Remarks</h4>
        <p>{{($productArr[0]->po_remarks)}}</p>
        @if(isset($productArr[0]->reason_to_close) && $productArr[0]->reason_to_close!='')
        <p>{{($productArr[0]->reason_to_close)}}</p>
        @endif
    </div>
</div>