@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/po/index">Purchases</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Purchase Orders</li>
        </ul>
    </div>
</div>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <?php
            $poStatus = $productArr[0]->po_status;
            ?>
            <div class="portlet-title">
                <div class="caption"> Purchase Order # {{$productArr[0]->po_code}}</div>
                <div class="pull-right margin-top-10">
                    @if(isset($featureAccess['closeFeature']) && $featureAccess['closeFeature'])
                    <?php
                    if(isset($productArr[0]->po_status) && $productArr[0]->po_status=='87005'){
                        $labelName = 'Close PO';
                    }else if(isset($productArr[0]->po_status) && $productArr[0]->po_status=='87001'){
                        $labelName = 'Cancel PO';
                    } 
                    ?>
                   <?php /* <button type="button" class="btn green-meadow" href="#closePOModel" data-toggle="modal" title="Close PO">{{$labelName}}</button> */?>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div id="ajaxResponse" style="display:none;" class="alert alert-danger">dfgh</div>
                <div class="tabbable-line">
                    <div class="tab-content">
                        @if($poStatus==87001)
                        <form id="po_form" method="POST" action="/po/updatePo">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="POST">
                            <input type="hidden" id="po_id" name="po_id" value="{{$productArr[0]->po_id}}"/>
                            <input type="hidden" name="po_type" id="po_type" value="{{$productArr[0]->po_type}}"/>
                            <input type="hidden" name="supplier_id" id="supplier_id" value="{{$productArr[0]->legal_entity_id}}"/>
                            <input type="hidden" name="warehouse_id" id="warehouse_id" value="{{$productArr[0]->le_wh_id}}"/>
                            <input type="hidden" id="supply_le_wh_id" name="supply_le_wh_id" value="{{$supply_le_wh_id}}"/>
                            <input type="hidden" name="stock_transfer_dc" id="stock_transfer_dc" value="{{$productArr[0]->stock_transfer_dc}}"/>
                            <div class="row">
                                <div class="col-md-4">
                                    <h4>
                                        Supplier 
                                            @if( $productArr[0]->po_status=='87001')
                                                <a id="po_supplier_edit" href="#update_supplier" data-toggle="modal" class="pull-right btn btn-square btn-icon-only btn-default "><i class="fa fa-pencil"></i></a>
                                            @endif
                                    </h4>
                                    <div class="well1 margin-top-10">
                                        <div class="table-scrollable-borderless">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td><strong> Name </strong></td>
                                                        <td> {{$supplier->business_legal_name}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top"><strong> Address </strong></td>
                                                        <td valign="top"> {{$supplier->address1}}, <br />{{$supplier->address2}},<br />
                                                            {{$supplier->city}}, {{$supplier->state_name}}, {{$supplier->country_name}} - {{$supplier->pincode}} 
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td><strong> Phone </strong></td>
                                                        <td> {{(isset($userInfo->mobile_no) ? $userInfo->mobile_no : '')}} </td>
                                                    </tr>

                                                    <tr>
                                                        <td><strong> Email </strong></td>
                                                        <td> {{(isset($userInfo->email_id) ? $userInfo->email_id : '')}} </td>
                                                    </tr>
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

                                                    @if(!empty($supplier->pan_number))
                                                    <tr>
                                                        <td><strong> PAN </strong></td>
                                                        <td>{{(!empty($supplier->pan_number) ? $supplier->pan_number : 'NA')}}</td>
                                                    </tr>
                                                    @endif
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
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <h4>Delivery Address</h4>
                                    <div class="well1 margin-top-10">
                                        <div class="table-scrollable-borderless">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td><strong> Name </strong></td>
                                                        <td>{{$whDetail->lp_wh_name}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top"><strong> Address </strong></td>
                                                        <td valign="top">{{$whDetail->address1}}, {{$whDetail->address2}},<br />
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
                                                        <td><strong> State&nbsp;Code</strong></td>
                                                        <td>{{$whDetail->state_code}}</td>
                                                    </tr>
                                                    @endif
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <h4>PO Details</h4>
                                    <div class="well1 margin-top-10">
                                        <div class="table-scrollable-borderless">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td><strong> PO Number </strong></td>
                                                        <td> {{$productArr[0]->po_code}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> PO Date </strong></td>
                                                        <td>{{Utility::dateFormat($productArr[0]->po_date)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Delivery Date </strong></td>
                                                        <td>{{Utility::dateFormat($productArr[0]->delivery_date)}}</td>
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
                                                        <td> {{($productArr[0]->po_type == 1 ? 'Qty Based' : 'Value Based')}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Created By </strong></td>
                                                        <td> {{$productArr[0]->user_name}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Status </strong></td>
                                                        <td> {{$po_status}} </td>
                                                    </tr>
                                                    @if($approvedStatus!='')
                                                    <tr>
                                                        <td><strong> Approval Status </strong></td>
                                                        <td> {{$approvedStatus}} </td>
                                                    </tr>
                                                    @endif

                                                    <tr>
                                                        <td><strong> DC to Supply </strong></td>
                                                        <td> 
                                                            {{$supply_dc_name}} 
                                                            @if( $productArr[0]->po_status=='87001')
                                                            <a id="po_supplier_edit" href="#update_supply_dc" data-toggle="modal" class="pull-right btn btn-square btn-icon-only btn-default "><i class="fa fa-pencil"></i></a>
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    @if($po_so_order_code != "")
                                                        <tr>
                                                            <td><strong> PO SO Code </strong></td>
                                                            <td> 
                                                                {{$po_so_order_code}} 
                                                                @if( $productArr[0]->po_status=='87001' && isset($featureAccess['poSOupdateFeature']) && $featureAccess['poSOupdateFeature'])
                                                                <a id="po_supplier_edit"  class="pull-right btn btn-square btn-icon-only btn-default" onclick="updatePoSoCode()"><i class="fa fa-trash-o"></i></a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td><strong> Stock Transfer Location </strong></td>
                                                        <td> 
                                                            {{$st_dc_name}} 
                                                            @if( $productArr[0]->po_status=='87001')
                                                            <a id="po_supplier_edit" href="#update_st_dc" data-toggle="modal" class="pull-right btn btn-square btn-icon-only btn-default "><i class="fa fa-pencil"></i></a>
                                                            @endif
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                             <?php 
                                $displayAcntInfo = '';
                                $displayduedate = 'style=display:none;';
                                $payment_mode = (isset($productArr[0]->payment_mode))?$productArr[0]->payment_mode:1;                                
                                $paid_through = (isset($productArr[0]->tlm_name) && $productArr[0]->tlm_name!='')?$productArr[0]->tlm_name.'==='.$productArr[0]->tlm_group:'';                                
                                $payment_type = (isset($productArr[0]->payment_type) && $productArr[0]->payment_type!=0)?$productArr[0]->payment_type:'';
                                $payment_ref = (isset($productArr[0]->payment_refno) && $productArr[0]->payment_refno!='')?$productArr[0]->payment_refno:'';
                                
                                if($payment_mode==1){ 
                                    $displayAcntInfo = 'style=display:none;';
                                    $displayduedate = '';
                                }
                                ?>
                            <div class="row inputfieldsmargtop">
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Payment Mode</strong></label>
                                        <select class="form-control select2me" data-live-search="true" id="payment_mode" name="payment_mode">
                                            <option value="1" <?php if($payment_mode==1){ echo 'selected'; } ?> >Post Paid</option>
                                            <option value="2" <?php if($payment_mode==2){ echo 'selected'; } ?> >Pre Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <?php /*
                                 @if(isset($ledgerAccounts) && count($ledgerAccounts)>0)
                                @if(isset($featureAccess['updatePaymentFeature']) && $featureAccess['updatePaymentFeature'])
                                <div id="paymentdiv" {{$displayAcntInfo}}>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Paid Through</label>
                                            <select class="form-control select2me" data-live-search="true" id="paid_through" name="paid_through">
                                                <option value="">Select Account</option>
                                                @foreach($ledgerAccounts as $account)                                
                                                <option value="{{ $account->tlm_name.'==='.$account->tlm_group }}">{{ $account->tlm_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Payment&nbsp;Type</label>
                                            <select class="form-control select2me" id="payment_type" name="payment_type">
                                                <option value="">Select Type</option>
                                                @foreach($paymentType as $key=>$payment)                                
                                                <option value="{{ $key }}">{{ $payment }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Reference No</label>
                                            <input type="text" class="form-control" id="payment_ref" name="payment_ref" value="{{$payment_ref}}"/>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endif
                                 */ ?>
                                <div id="paymentduediv" {{$displayduedate}}>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label"><strong>Payment Due Date</strong></label>
                                            <div class="input-icon right">
                                                <i class="fa fa-calendar"></i>
                                                <?php $duedate = ($productArr[0]->payment_due_date!='0000-00-00 00:00:00' && $productArr[0]->payment_due_date!='')?date('m/d/Y',strtotime($productArr[0]->payment_due_date)):date('m/d/Y');?>
                                                <input type="text" class="form-control" name="payment_due_date" id="payment_due_date" value="{{$duedate}}" placeholder="Payment Due Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Logistic Cost</strong></label>
                                        <input type="number" min="0" value="{{$productArr[0]->logistics_cost}}" style="width:110px" class="form-control" name="logistics_cost" id="logistics_cost"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Proforma Invoice(Quote)</strong></label>
                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                            <div>
                                                <span class="btn default btn-file btn green-meadow">
                                                    <span class="fileinput-new">Choose File</span>
                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Change File</span>
                                                    <input class="form-control" type="file" id="proforma" name="proforma" placeholder="Proof of Document">
                                                </span>
                                                <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                <div class="thumbnail">
                                                    <div id="doc_text">
                                                        @if(isset($poDocs) && count($poDocs)>0)
                                                            @foreach($poDocs as $docs)
                                                            <div>
                                                                <span><i class="fa fa-close downloadclose" data-doc_id="{{$docs->doc_id}}"></i></span>
                                                                <a href="{{$docs->file_path}}" target="_blank" class="closedownload" ><i class="fa fa-download"></i></a>
                                                            </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                            
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <div class="form-group">
                                        <br><br>
                                        <button type="button" id="addskupopupbtn" href="#basicvalCodeModal3" data-toggle="modal" class="btn green-meadow">Add SKU </button>
                                    </div>
                                </div>
                            </div>
                            <span style="float:right;font-size: 11px; font-weight: bold;">* All Amounts in ({{$curSymbol}}) </span>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Product Details</h4>
									
                                    <div class="parent">
                                        <table class="table table-inverse table-bordered table-hover fixTable" id="product_list" style="white-space:nowrap;">
                                            <thead>
                                                <tr>
                                                    <th>SNo</th>
                                                    <th>SKU</th>
                                                    <th>Product&nbsp;Name</th>
                                                    <th>Qty</th>
                                                    <th>Free&nbsp;Qty</th>
                                                    <th>Current SOH</th>
                                                    <th>MRP</th>
                                                    <th>{{Lang::get('headings.LP')}}</th>
                                                    <th title="Previous {{Lang::get('headings.LP')}} across all suppliers & dc">Previous&nbsp;{{Lang::get('headings.LP')}}</th>                                                    
                                                    <th class="potypeshow">Price</th>
                                                    <th class="potypeshow">Sub&nbsp;Total</th>
                                                    <th class="potypeshow">Tax%</th>
                                                    <th class="potypeshow">Tax</th>
                                                    <th class="potypeshow">Apply Disc.</th>
                                                    <th class="potypeshow">Discount</th>
                                                    <th class="potypeshow">Total</th>
                                                    <th class="">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sno = 1;
                                                $sumOfSubtotal = 0;
                                                $sumOfTaxtotal = 0;
                                                $sumOfGrandtotal = 0;
                                                $taxPercentage = 0;
                                                $totQty = 0;
                                                $grandTotal = 0;
                                                $discount_before_tax = (isset($productArr[0]->discount_before_tax) && $productArr[0]->discount_before_tax == 1)?$productArr[0]->discount_before_tax:0;
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
                                                ?>
                                                @foreach($productArr as $product)
                                                <?php
                                                #echo '<pre/>';print_r($product);die;
                                                $product_id = $product->product_id;
                                                $sumTax = 0;

                                                $qty = ($product->qty != '') ? $product->qty : 0;
                                                $free_qty = ($product->free_qty != '') ? $product->free_qty : 0;

                                                $base_price = (isset($product->price) && $product->price != '') ? $product->price : 0;
                                                
                                                $prev_elp = (isset($product->prev_elp) && $product->prev_elp != '') ? $product->prev_elp : 0;
                                                $uomOptions = isset($uom[$product_id]) ? $uom[$product_id] : '';
                                                $freeUOMOptions = isset($freeuom[$product_id]) ? $freeuom[$product_id] : '';

                                                $isTaxInclude = (isset($product->is_tax_included))?$product->is_tax_included:0;
                                                if ($isTaxInclude == 1) {
                                                    $current_elp = $product->unit_price;
                                                }else{
                                                    $current_elp = $product->unit_price+(($product->unit_price*$product->tax_per)/100);
                                                }
                                                $discAmt=0;
                                                $sub_total = $product->sub_total;                                                
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
                                                        $contribution = ($totalAfteritemDisc/$grandTotal);
                                                        $discAmt = $discAmt+($product->discount*$contribution);
                                                    }
                                                }
                                                $no_of_eaches = ($product->no_of_eaches == 0 || $product->no_of_eaches == '') ? 1 : $product->no_of_eaches;
                                                $unit_disc = $discAmt/($qty * $no_of_eaches);
                                                $current_elp = ($current_elp-$unit_disc);// curelp is recalculating to make old data correct which dont have cur_elp
                                                if($discount_before_tax==1){
                                                    $current_elp = $product->cur_elp;
                                                }
                                                $current_elp_colour = number_format($current_elp,3);
                                                $prev_elp_colour = number_format($product->prev_elp,3);
                                                if ($current_elp_colour>$prev_elp_colour) {
                                                    $css_colour = "style='color:red'";
                                                }else{
                                                    $css_colour="";
                                                    }
                                                $taxper = 0;
                                                $taxname = '';
                                                $taxAmt = (isset($product->tax_amt))?$product->tax_amt:0;
                                                $taxincludecheck = ($isTaxInclude==1)?'checked':'';
                                                $hsn_code = '';
                                                $tax_data = '';
                                                $tax_code = '';
                                                if (isset($taxArr[$product_id]) && is_array($taxArr[$product_id])) {
                                                    foreach ($taxArr[$product_id] as $tax) {
                                                        $sumTax = $sumTax + $tax['Tax Percentage'];
                                                        //$taxAmt = (isset($taxTypeAmt[''.$tax['Tax Percentage'].'']))? $taxTypeAmt[''.$tax['Tax Percentage'].'']:0;
                                                        //$taxTypeAmt[''.$tax['Tax Percentage'].''] = $taxAmt+$product->unit_price * $product->qty;
                                                        $taxname .= $tax['Tax Type'].',';
                                                        $taxper = $taxper+$tax['Tax Percentage'];
                                                        if($tax_code==''){
                                                            $tax_code = isset($tax['Tax Code'])?$tax['Tax Code']:'';
                                                        }
                                                        if($hsn_code==''){
                                                            $hsn_code = isset($tax['HSN_Code'])?$tax['HSN_Code']:'';
                                                        }
                                                    }
                                                    $taxname = trim($taxname,',');
                                                    $tax_data = base64_encode(json_encode($taxArr[$product_id]));
                                                }
                                                ?>
                                                <tr class="odd gradeX">
                                                    <td align="center"><span class="snos">{{$sno++}}</span> 
                                                    </td>
                                                    <td>{{$product->sku}}<input type="hidden" name="po_product_id[]" value="{{$product_id}}">
                                                        <input type="hidden" id="product_sku{{$product_id}}" value="{{$product->sku}}">
                                                        <input type="hidden" name="parent_id[]" value="{{$product->parent_id}}"></td>
                                                    <td>{{$product->product_title}}</td>
                                                    <td align="center"> 
                                                        <div style="width:175px">
                                                            <div style="float:left">
                                                                <input class="form-control" id="qty{{$product_id}}" min="1" type="number" size="3" style="width:70px;" value="{{$product->qty}}" name="qty[]">
                                                            </div>
                                                            <div style="float:right">
                                                                <select class="form-control packsize{{$product_id}}" name="packsize[]" required="required" style="width:100px;"><?php echo $uomOptions; ?></select>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td> 
                                                        <div style="width:175px">
                                                            <div style="float:left">
                                                                <input class="form-control" id="freeqty{{$product_id}}" min="0" type="number" size="3" style="width:70px;" value="{{$free_qty}}" name="freeqty[]">
                                                            </div>
                                                            <div style="float:right">
                                                                <select class="form-control freepacksize{{$product_id}}" name="freepacksize[]" required="required" style="width:100px;"><?php echo $freeUOMOptions; ?></select>
                                                            </div>
                                                        </div>                    
                                                    </td>
                                                    <td>{{$product->final_soh}}</td>
                                                    <td align="right">{{number_format($product->mrp, 4)}}</td>

                                                    <td align="right"><span id="curelptext{{$product_id}}" <?php echo $css_colour?>  >{{number_format($current_elp, 3)}}</span>
                                                    <input type="hidden" name="curelpval[{{$product_id}}]" id="curelpval{{$product_id}}" value="{{$current_elp}}"/></td>
                                                    <td align="right">{{number_format($prev_elp, 4)}}<input type="hidden" name="prev_elp_val[{{$product_id}}]" id="prev_elp_val{{$product_id}}" value="{{number_format($prev_elp, 4)}}"/> </td>                                                    
                                                    <td class="potypeshow">
                                                        <div style="width:170px">
                                                            <div style="float:left">
                                                                <input class="form-control pobaseprice" min="0" step="any" id="baseprice{{$product_id}}" style="width:100px;" name="po_baseprice[]" type="number" value="{{$product->price}}">
                                                            </div>
                                                            <div style="float:left">
                                                                <input class="pretax pretax{{$product_id}}" data-id="{{$product_id}}" {{$taxincludecheck}} name="pretax[{{$product_id}}]" type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                                                            </div>
                                                            <div style="float:left"><span style="margin-top:8px; font-size:9px;">Incl.Tax </span>  </div>
                                                        </div>
                                                        <input id="taxname{{$product_id}}" name="po_taxname[{{$product_id}}]" type="hidden" value="{{$taxname}}">
                                                        <input id="taxdata{{$product_id}}" name="po_taxdata[{{$product_id}}]" type="hidden" value="{{$tax_data}}">
                                                        <input id="hsn_code{{$product_id}}" name="hsn_code[{{$product_id}}]" type="hidden" class="hsn_code" value="{{$hsn_code}}">
                                                        <input id="tax_code{{$product_id}}" name="tax_code[{{$product_id}}]" type="hidden" class="tax_code" value="{{$tax_code}}">
                                                        <input id="mrp{{$product_id}}" type="hidden" value="{{$product->mrp}}">
                                                        <input id="unit_price{{$product_id}}" name="unit_price[{{$product_id}}]" class="unitPrice" data-product_id="{{$product_id}}" type="hidden" value="{{$product->unit_price}}">
                                                        <input id="taxper{{$product_id}}" name="po_taxper[{{$product_id}}]" type="hidden" value="{{$sumTax}}">
                                                        <input name="po_taxvalue[{{$product_id}}]" id="taxval{{$product_id}}" type="hidden" value="{{$taxAmt}}">
                                                    </td>
                                                    <td class="potypeshow" align="right"><span id="totalPriceText{{$product_id}}">{{($product->sub_total-$taxAmt)}}</span></td>
                                                    <td class="potypeshow" align="center">
                                                        {{$taxname.'@'.(float)$taxper}}
                                                    </td>
                                                    <td class="potypeshow" align="right"><span id="taxtext{{$product_id}}">{{number_format($taxAmt, 4)}}</span></td>
                                                    <td class="potypeshow" align="right"><input class="apply_discount_item" data-id="{{$product_id}}" <?php if($product->apply_discount==1) echo 'checked'; ?> name="apply_discount[{{$product_id}}]" type="checkbox" value="1" style="margin:7px 10px 0px 10px;"></td>
                                                    <td class="potypeshow" align="right">
                                                        <div style="width:170px">
                                                            <div style="float:left"><input class="form-control item_discount" min="0" id="discount{{$product_id}}" style="width:100px;" name="item_discount[{{$product_id}}]" type="number" value="{{$product->item_discount}}"></div>
                                                            <div style="float:left"><input class="item_discount_type" <?php if($product->item_discount_type==1) echo 'checked'; ?> name="item_discount_type[{{$product_id}}]" type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                                                            <input class="" id="item_discount_amt{{$product_id}}" name="" type="hidden" value="0">
                                                            </div>
                                                            <div style="float:left"><span style="margin-top:8px; font-size:9px;"><strong>%</strong></span>  </div>
                                                        </div>
                                                    </td>
                                                    <td class="potypeshow"align="right" style="background:#fbfcfd !important;" >
                                                        <input class="form-control pototprice" min="0" step="any" id="totprice{{$product_id}}" style="width:100px;" name="po_totprice[]" type="number" value="{{($product->sub_total)}}">
                                                    </td>
                                                    <td class="" align="center" style="background:#fbfcfd !important;width: 60px !important;">
                                                        <a class="fa fa-trash-o delete_product" data-id="{{$product_id}}"></a>
                                                    </td>
                                                </tr>
                                                @endforeach   

                                            </tbody>	
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-md-2">                       
                                    <div class="form-group">
                                        <label class="control-label"><strong>Apply discount on bill</strong></label>
                                        <input class="apply_discount" <?php if(isset($productArr[0]->apply_discount_on_bill)&&$productArr[0]->apply_discount_on_bill==1){ echo 'checked'; } ?> name="apply_bill_discount" type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                                    </div>
                                    </div>
                                 <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Discount</strong></label>
                                        <div style="float:left"><input class="form-control" min="0" id="discount" style="width:100px;" name="bill_discount" type="number" value="{{$productArr[0]->discount}}"></div>
                                        <div style="float:left"><input class="bill_discount_type" <?php if(isset($productArr[0]->discount_type)&&$productArr[0]->discount_type==1){ echo 'checked'; } ?> name="bill_discount_type" type="checkbox" value="1"  style="margin:7px 6px 0px 10px;"></div>
                                        <div style="float:left"><span style="margin-top:8px; font-size:9px;"><strong>%</strong></span>  </div>
                                    </div>
                                </div>
                                <div class="col-md-2">                       
                                    <div class="form-group">
                                        <label class="control-label"><strong>Discount Before Tax</strong></label>
                                        <input class="apply_discount" name="discount_before_tax" <?php if(isset($productArr[0]->discount_before_tax)&&$productArr[0]->discount_before_tax==1){ echo 'checked'; } ?> type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                                    </div>
                                </div>
                                <div class="col-md-2">                       
                                    <div class="form-group">
                                        <label class="control-label"><strong>Stock Transfer</strong></label>
                                        <input class="stock_transfer" name="stock_transfer" <?php if(isset($productArr[0]->stock_transfer)&&$productArr[0]->stock_transfer==1){ echo 'checked'; } ?> type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                                        <input  name="po_so_order_code" id="po_so_order_code" type="hidden" value="{{$productArr[0]->po_so_order_code}}" style="margin:7px 10px 0px 10px;">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Total Price :</strong> <span id="total_sub_total"></span></label> 
                                    </div>    
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Total Tax :</strong> <span id="tax_total"></span></label>
                                
                                    </div>    
                                </div> 
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Grand Total :</strong> <span id="grand_total"></span></label>
                               
                                    </div>    
                                </div>    
                            </div>
                            <div class="row">
                            <div class="col-md-4">
                                <h4>Remarks</h4>
                                <textarea class="form-control" name="po_remarks" cols="60" id="po_remarks" rows="2">{{($productArr[0]->po_remarks)}}</textarea>
                                </div>
                            </div>
                            <div class="row" style="margin-top:10px;">
                                <hr />
                                <div id="deleted_products"></div>
                                <div class="col-md-12 text-center">
                                    <input type="hidden" name="" id="sno_increment" value="{{$sno}}">
                                    <button type="submit" class="btn green-meadow" name="Save" id="save">Update</button>
                                    <a class="btn green-meadow" href="/po/index">Cancel</a>
                                </div>
                            </div>
                        </form>
                        
                        @else                    
                        <div class="col-md-12 alert alert-danger">
                                PO Can't Be Edit
                            </div>
                        @endif                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="modal modal-scroll fade in" id="basicvalCodeModal3" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="basicvalCode">Add SKU</h4>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12">
                            <div style="display:none;" id="error-msg" class="alert alert-danger"></div>                        
                            <div class="form-group">
                                <label class="control-label"><strong>SKU </strong><span class="required">*</span></label>
                                <input type="text" id="search_sku" class="form-control" placeholder="SKU,Product Name,UPC" />
                                <input type="hidden" id="addproduct_id" class="form-control" placeholder="SKU,Product Name,UPC" />
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"><strong>Article Number </strong><span class="required">*</span></label>
                            <span id="prod_sku"></span>
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"><strong>Brand </strong><span class="required">*</span></label>
                            <span id="prod_brand"></span>
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"><strong>MRP </strong><span class="required">*</span></label>
                            <span id="prod_mrp"></span>
                        </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button class="btn green-meadow" id="addSkubtn">Add</button>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <?php /*
    <div class="modal modal-scroll fade in" id="closePOModel" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="basicvalCode">Close PO</h4>
                </div>
                <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div style="display:none;" id="error-msg1" class="alert alert-danger"></div>                       
                                <div class="form-group">
                                    <label class="control-label">Status <span class="required">*</span></label>
                                    <select name="po_status_val" id="po_status_val" class="form-control">
                                        @if(isset($productArr[0]->po_status) && $productArr[0]->po_status=='87005')
                                        <option value="1">Close PO</option>
                                        @endif
                                        @if(isset($productArr[0]->po_status) && $productArr[0]->po_status=='87001')
                                        <option value="87004">Cancel PO</option>
                                        @endif
                                    </select>
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Reason <span class="required">*</span></label>
                                    <textarea class="form-control" name="close_reason" id="close_reason"></textarea>
                                </div>
                            </div> 
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button class="btn green-meadow" id="closePObtn">Submit</button>
                            </div>
                        </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    */ ?>

    @if( $productArr[0]->po_status=='87001')
        <div class="modal fade" id="update_supplier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Update Supplier</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{ Form::open(array('url' => '#', 'id' => 'update_supplier_form'))}}

                            <div class="col-md-12">                        
                                <div class="form-group">
                                    <select name="supp_name" id="supp_name" class="form-control select2me" placeholder="Select Supplier">
                                        <option value="">Please Select Supplier</option>
                                        @foreach ($suppliers_list as $supp_data)
                                        <option value="{{ $supp_data['legal_entity_id'] }}" <?php if($supplier->legal_entity_id == $supp_data['legal_entity_id'] ) echo 'selected' ?>>{{ $supp_data['business_legal_name'] }} - {{ isset($supp_data['le_code'])? $supp_data['le_code'] : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="po_id" name="po_id" value="{{$productArr[0]->po_id}}"/>
                            <input type="hidden" id="supplier_id" name="supplier_id" value="{{$supplier->legal_entity_id}}"/>
                            <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{$productArr[0]->le_wh_id}}"/>
                            <input type="hidden" id="supply_le_wh_id" name="supply_le_wh_id" value="{{$supply_le_wh_id}}"/>
                            <input type="hidden" id="stock_transfer" name="stock_transfer" value="{{$productArr[0]->stock_transfer}}"/>
                            <input type="hidden" id="stock_transfer_dc" name="stock_transfer_dc" value="{{$stock_transfer_dc}}"/>
                        </div>
                        <div class="row">
                            <hr/>
                            <div class="col-md-12 text-center"> 
                                <button type="button" class="btn green-meadow btnn" id="update_supplier_btn" onclick="updateSupplier()">Submit
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="update_supply_dc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Update Supply Dc</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{ Form::open(array('url' => '#', 'id' => 'update_supply_dc_form'))}}

                            <div class="col-md-12">                        
                                <div class="form-group">
                                    <select name="supply_dc_name" id="supply_dc_name" class="form-control select2me" placeholder="Select Supplier">
                                        <option value="">Please Select Supplier</option>
                                        @foreach ($warehouseList as $supp_dc_data)
                                        <option value="{{ @$supp_dc_data->le_wh_id }}" <?php if(@$supply_le_wh_id == @$supp_dc_data->le_wh_id ) echo 'selected' ?>>{{ @$supp_dc_data->lp_wh_name }} - {{@$supp_dc_data->le_wh_code }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="po_id" name="po_id" value="{{$productArr[0]->po_id}}"/>

                            <input type="hidden" id="supplier_id" name="supplier_id" value="{{$supplier->legal_entity_id}}"/>
                            <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{$productArr[0]->le_wh_id}}"/>
                            <input type="hidden" id="supply_le_wh_id" name="supply_le_wh_id" value="{{$supply_le_wh_id}}"/>
                            <input type="hidden" id="stock_transfer" name="stock_transfer" value="{{$productArr[0]->stock_transfer}}"/>
                            <input type="hidden" id="stock_transfer_dc" name="stock_transfer_dc" value="{{$stock_transfer_dc}}"/>
                        </div>
                        <div class="row">
                            <hr/>
                            <div class="col-md-12 text-center"> 
                                <button type="button" class="btn green-meadow btnn" id="update_supplier_btn" onclick="updateSuppyDc()">Submit
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="update_st_dc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Update Stock Transfer Location</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{ Form::open(array('url' => '#', 'id' => 'update_st_dc_form'))}}

                            <div class="col-md-12">                        
                                <div class="form-group">
                                    <select name="st_dc_name" id="st_dc_name" class="form-control select2me" placeholder="Select Stock Transfer Location">
                                        <option value="">Select Stock Transfer Location</option>
                                        @foreach ($warehouseList as $supp_dc_data)
                                        <option value="{{ @$supp_dc_data->le_wh_id }}" <?php if(@$stock_transfer_dc == @$supp_dc_data->le_wh_id ) echo 'selected' ?>>{{ @$supp_dc_data->lp_wh_name }} - {{@$supp_dc_data->le_wh_code }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="po_id" name="po_id" value="{{$productArr[0]->po_id}}"/>
                            <input type="hidden" id="supplier_id" name="supplier_id" value="{{$supplier->legal_entity_id}}"/>
                            <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{$productArr[0]->le_wh_id}}"/>
                            <input type="hidden" id="supply_le_wh_id" name="supply_le_wh_id" value="{{$supply_le_wh_id}}"/>
                            <input type="hidden" id="stock_transfer" name="stock_transfer" value="{{$productArr[0]->stock_transfer}}"/>
                            <input type="hidden" id="stock_transfer_dc" name="stock_transfer_dc" value="{{$stock_transfer_dc}}"/>
                        </div>
                        <div class="row">
                            <hr/>
                            <div class="col-md-12 text-center"> 
                                <button type="button" class="btn green-meadow btnn" id="update_supplier_btn" onclick="updateStDc()">Submit
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('PurchaseOrder::Form.poErrorModelPopup')
    @stop

@section('style')

<style type="text/css">
#po_supplier_edit {
    border: 0px;
    padding: 0;
    height: auto;
}
table > thead > tr > th { background:#efefef;}

.parent {height: auto;}
.fixTable {width: 1800px !important;}
    .closedownload{ border:1px solid #ddd; width:25px; height:25px; padding:5px; margin:5px 0px;}   
    #doc_text{ float:left;width:300px; display:flex; }
    .thumbnail {
        border: none !important;
    }
    .downloadclose{
        position: relative;
        left: 34px;
        top: 9px; font-size:12px !important;color:#F3565D;
        text-decoration: none;
        cursor: pointer;
    }
    .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
    .loderholder img{ position: absolute; top:50%;left:50%; }
        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        border-top: 0px !important;
    }
        .favfont i{font-size:18px !important; color:#5b9bd1 !important;}
        .tabbable-line > .nav-tabs > li > a > i {
            color: #5b9bd1 !important;
        }
        .inputfieldsmargtop{ margin-top: 20px;}
        .well1 {
        height:365px;
        border: 1px solid #eee !important;
        background:none !important;
        border-radius: 0px !important;
        }
        .well {
            border-radius: 0px !important;
        }

        .imgborder{border:1px solid #ddd !important;}
        .tabs-left.nav-tabs > li.active > a, .tabs-left.nav-tabs > li.active > a:hover > li.active > a:focus {
            border-radius: 0px !important;  
        }
        .nav>li>a:visited{
            color:red !important;
        }
        tabs.nav>li>a {
            padding-left: 10px !important;
        }
        .note.note-success {
            background-color: #c0edf1 !important;
            border-color: #58d0da !important;
            color: #000 !important;
        }
        hr {
            margin-top:0px !important;
            margin-bottom:10px !important;
        }
        .portlet > .portlet-title {
            border-bottom: 0px !important;
        }
        .newproduct{color: blue;font-weight: bold;}
    
        .table > tbody > tr > td{
            line-height: 1.4 !important;
            font-size: 12px !important;
        }
        .rightAlignment { text-align: right;}
        .centerAlignment { text-align: center;}
        .ui-autocomplete{
        z-index: 10100 !important; top:10px;  height:100px; overflow-y: scroll; overflow-x:hidden; border-top:none!important;
        position:fixed !important;
    }


  .ui-autocomplete li{ border-bottom:1px solid #efefef; padding-top:10px!important; padding-bottom:10px!important; 
    }
    .newproduct{color: blue;font-weight: bold;}

    </style>
    <link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
    @stop

    @section('userscript')
	<script src="{{ URL::asset('assets/admin/pages/scripts/tableHeadFixer.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/jquery-numberformat/jquery.number.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>

    <script>
var skus='';

var basePrice = 0;
var totalPrice = 0;
var qty = 0;
var noofeach = 0;
var freeqty =0;
var freenoofeach = 0;
var taxper = 0;
var taxAmt = 0;
var totalAmt = 0;
var pre_post_type = 0;    
var totfreeqty = 0;
var totpoqty = 0;
var qtycalculate = 0;
var eachprice =0;
var current_elp=0;
var price =0;
var apply_discount =0;
var item_discount_type =0;
var item_discount =0;
var apply_bill_discount =0;
var bill_discount_type =0;
var bill_discount =0;
var discount_before_tax =0;
var discAmt = 0;
    
function deleteChildProduct(product_id) {
        $("input[name='po_product_id[]']").each(function () {
            var parent_id = $(this).closest('tr').find('input[name="parent_id[]"]').val();
            if(product_id==parent_id) {
                var childproduct_id = $(this).val();
                var newproductadd = $(this).closest('tr').find('input[name="newproductadd[]"]').val();
                if(childproduct_id!=newproductadd){
                    $('#deleted_products').append('<input type="hidden" name="delete_product[]" value="'+childproduct_id+'">');
                }
                $(this).closest('tr').remove();
            }
        });
    }
    function readValues(product_id){
        $('#save').attr('disabled',true);
        basePrice = parseFloat($('#baseprice'+product_id).val());
        totalPrice = $('#totprice'+product_id).val();
        qty = $('#qty'+product_id).val();
        noofeach = parseInt($('.packsize'+product_id).find(':selected').attr('data-noofeach'));
        if(!noofeach && isNaN(noofeach)){
            noofeach = 0;
        }
        freeqty = $('#freeqty'+product_id).val();
        freenoofeach = parseInt($('.freepacksize'+product_id).find(':selected').attr('data-noofeach'));
        if(!freenoofeach && isNaN(freenoofeach)){
            freenoofeach = 0;
        }        
        taxper = parseFloat($('#taxper'+product_id).val());
        console.log('taxper==='+taxper);
        pre_post_type = $('input[name="pretax['+product_id+']"]:checked').val();
        totfreeqty = parseInt(freeqty*freenoofeach);
        totpoqty = parseInt(qty * noofeach);
        qtycalculate = totpoqty - totfreeqty;
        eachprice =parseFloat(basePrice /noofeach); 
        price =parseFloat(eachprice * qtycalculate);
        
        apply_discount = $('input[name="apply_discount['+product_id+']"]:checked').val();
        item_discount_type = $('input[name="item_discount_type['+product_id+']"]:checked').val();
        item_discount = $('input[name="item_discount['+product_id+']"]').val();
        apply_bill_discount = $('input[name="apply_bill_discount"]:checked').val();
        bill_discount_type = $('input[name="bill_discount_type"]:checked').val();
        bill_discount = $('input[name="bill_discount"]').val();
        discount_before_tax = $('input[name="discount_before_tax"]:checked').val();
        discAmt=0;
    }
    function getGrandTotal(){
        var grandTotal = 0;
        $("input[name='po_product_id[]']").each(function() {
            var product_id = $(this).val();
            grandTotal += (parseFloat($('#totprice'+product_id).val()) - parseFloat($('#item_discount_amt'+product_id).val()));
        });
        return grandTotal;
    }
    function calcOnFinalSubmit(){
        $("input[name='po_product_id[]']").each(function() {
            var product_id = $(this).val();
            readValues(product_id);
            calcTotal(product_id);
            console.log('inside final submit');
        });
        console.log('returnnnn');
        return true;
    }
    function calcTotal(product_id){
        console.log('totqty===='+totpoqty+'freeqty==='+freeqty+'freeeaches=='+freenoofeach);
        if(totpoqty>=totfreeqty){
            totalAmt =(eachprice*qtycalculate);
            console.log(product_id+'==price'+price+'==taxper'+taxper+'===Each=='+eachprice);
            pre_post_type = (pre_post_type) ? pre_post_type : 0;
            if(pre_post_type==0){
                taxAmt = parseFloat((totalAmt*taxper)/100);
                totalAmt = parseFloat(totalAmt+taxAmt);
                current_elp = eachprice+((eachprice*taxper)/100);
            }else{
                var price_excltax = parseFloat(totalAmt/(1+((taxper*1)/100)));
                console.log('prrr='+totalAmt+'ttax='+price_excltax);
                taxAmt = parseFloat(totalAmt-price_excltax);
                totalAmt = parseFloat(totalAmt);
                current_elp = eachprice;
            }
            $('#unit_price'+product_id).val(eachprice);
            $('#totalPriceText'+product_id).text(price);            
            $('#taxtext'+product_id).text($.number(taxAmt,5));
            $('#taxval'+product_id).val(taxAmt);
            $('#totprice'+product_id).val(totalAmt);
            $('#totalval'+product_id).text($.number(totalAmt,5));
            // if(discount_before_tax!=1){
                if(apply_discount==1){
                    if(item_discount_type==1){
                        discAmt = (totalAmt*item_discount)/100;
                    }else{
                        discAmt = item_discount;
                    }
                }
                $('#item_discount_amt'+product_id).val(discAmt);
                var totalAfteritemDisc = parseFloat(totalAmt-discAmt);
                if(apply_bill_discount==1){
                    if(bill_discount_type==1){
                        discAmt = parseFloat(discAmt)+parseFloat((totalAfteritemDisc*bill_discount)/100);
                    }else{
                        var grandTotal= getGrandTotal();
                        var contribution = (totalAfteritemDisc/grandTotal);
                        discAmt = parseFloat(discAmt)+parseFloat((bill_discount*contribution));
                    }
                }
            // }
            var unit_disc = parseFloat(discAmt/totpoqty);
            current_elp = (current_elp-unit_disc);
            $('#curelptext'+product_id).text($.number(current_elp,5));
            $('#curelpval'+product_id).val(current_elp);
            if(parseFloat($('#curelpval'+product_id).val()) > parseFloat($('#prev_elp_val'+product_id).val())){
                document.getElementById('curelptext'+product_id).style.color='red';
            }else{
                document.getElementById('curelptext'+product_id).style.color='';
            }
            console.log(product_id+'==TotT'+taxAmt+'==TotA'+totalAmt+'==='+pre_post_type);
            calTotalvalues();
            $('#save').attr('disabled',false);
           

            return false;
        }else{
            alert('Free Qty should not be morethan po Qty');
            $('#freeqty'+product_id).val(0);
            readValues(product_id);
            calcTotal(product_id);
            return false;
        }
    }
    function calcPrice(product_id){
        var totalPrice = $('#totprice'+product_id).val();
        console.log(totalPrice);
        var price_excltax = parseFloat(totalPrice/(1+((taxper*1)/100)));
        taxAmt = parseFloat(totalPrice - price_excltax);
        qtycalculate = totpoqty - totfreeqty;
        eachprice =parseFloat((totalPrice / qtycalculate));            
        var price =parseFloat(eachprice*noofeach);
        
        $('#unit_price'+product_id).val(eachprice);        
        $('#baseprice'+product_id).val(price);
        $('#totalPriceText'+product_id).text(price);
        $('#taxtext'+product_id).text($.number(taxAmt,5));
        $('#taxval'+product_id).val(taxAmt);
        // if(discount_before_tax!=1){
            if(apply_discount==1){
                if(item_discount_type==1){
                    discAmt = (totalPrice*item_discount)/100;
                }else{
                    discAmt = item_discount;
                }
            }
            $('#item_discount_amt'+product_id).val(discAmt);
            var totalAfteritemDisc = parseFloat(totalPrice-discAmt);
            if(apply_bill_discount==1){
                if(bill_discount_type==1){
                    discAmt = parseFloat(discAmt)+parseFloat((totalAfteritemDisc*bill_discount)/100);
                }else{
                    var grandTotal= getGrandTotal();
                    var contribution = (totalAfteritemDisc/grandTotal);
                    discAmt = parseFloat(discAmt)+parseFloat((bill_discount*contribution));
                }
            }
        // }
        var unit_disc = parseFloat(discAmt/totpoqty);
        current_elp = (eachprice-unit_disc);
        $('#curelptext'+product_id).text($.number(current_elp,5));
        $('#curelpval'+product_id).val(current_elp);
        if(parseFloat($('#curelpval'+product_id).val()) > parseFloat($('#prev_elp_val'+product_id).val())){
                document.getElementById('curelptext'+product_id).style.color='red';
            }else{
                document.getElementById('curelptext'+product_id).style.color='';
            }
        console.log(product_id+'==TotT'+taxAmt+'==TotA'+totalAmt+'==='+pre_post_type);
        calTotalvalues();
        $('#save').attr('disabled',false);
      
        return false;
    }
$(document).ready(function () {
    $(".fixTable").tableHeadFixer({"head" : false, "right" : 2});
    $('#payment_due_date').datepicker({
        minDate: 0,
        numberOfMonths: 1,
        onSelect: function(dateText) {
       }
    });
    $('#addproduct_id').val('');
    $('#search_sku').val('');
    calTotalvalues();
    
    $(document).on('change','.pretax,.pobaseprice,input[name="qty[]"],input[name="freeqty[]"],[name="freepacksize[]"],.item_discount,.item_discount_type,.apply_discount_item',function(){
        var product_id = $(this).closest('tr').find('input[name="po_product_id[]"]').val();
        console.log(product_id);
        readValues(product_id);
        calcTotal(product_id);
    });
    $(document).on('change','[name="packsize[]"]',function(){
        var product_id = $(this).closest('tr').find('input[name="po_product_id[]"]').val();
        var noofeach = parseInt($('.packsize'+product_id).find(':selected').attr('data-noofeach'));
        var unit_price = parseFloat($('#unit_price'+product_id).val());
        console.log('unit='+unit_price+'==eachesss='+noofeach);
        $('#baseprice'+product_id).val(unit_price*noofeach);
        readValues(product_id);
        calcTotal(product_id);
    });
    $(document).on('change','[name="po_totprice[]"]',function(){
        var product_id = $(this).closest('tr').find('input[name="po_product_id[]"]').val();
        $('.pretax'+product_id).prop('checked',true);
        readValues(product_id);       
        calcPrice(product_id);
    });
    $(document).on('click','.delete_product',function(){
        var product_id = $(this).attr('data-id');
        var reference = $(this);
        var po_id = $('#po_id').val();
        if(confirm('Do you want to remove item?')){
            var newproductadd = reference.closest('tr').find('input[name="newproductadd[]"]').val();
            if(product_id!=newproductadd){
                $('#deleted_products').append('<input type="hidden" name="delete_product[]" value="'+product_id+'">');
            }
            reference.closest('tr').remove();
            deleteChildProduct(product_id);
            calTotalvalues();
            snoCount();
        }        
    });
    $(document).on('change','input[name="apply_bill_discount"],input[name="bill_discount"],[name="bill_discount_type"],[name="discount_before_tax"]',function(){
        $("input[name='po_totprice[]']").each(function() {
            $(this).trigger('change');
        });
    });    
    $('#po_form').validate({
            rules: {
                po_id: {
                    required: true
                },
                date: {
                    required: true
                },
                supplier_list: {
                    required: true
                },
                warehouse_list: {
                    required: true
                },
            },
            submitHandler: function (form) {
                var form = $('#po_form');
                if(calcOnFinalSubmit()){
                if(checkProductQty()){
                    if(checkProductUOM()){
                        if(checkProductTax()){
                        if(checkProductMRP()){
                            $('.loderholder').show();
                            $.post(form.attr('action'), form.serialize(), function (data) {
                                data = jQuery.parseJSON(data);
                                 if (data.status == 200) {
                                     $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                                     $('html, body').animate({scrollTop: '0px'}, 500);
                                     window.setTimeout(function(){window.location.href = '/po/details/' + data.po_id},1000);
                                 } else {
                                     $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html(data.message).show();                                     
                                     $('html, body').animate({scrollTop: '0px'}, 500);
                                     $('.loderholder').hide();
                                 }
                             });
                        }else{
                            $('#ajaxResponse').html("{{Lang::get('po.alertPOMRP')}}"+" for "+skus).show();
                            $('.loderholder').hide();
                            $('html, body').animate({scrollTop: '0px'}, 500);
                        }
                        }else{
                            $('#ajaxResponse').html("Could not find Tax info "+" for "+skus).show();
                            $('.loderholder').hide();
                            $('html, body').animate({scrollTop: '0px'}, 500);
                        }
                    }else{
                        $('#ajaxResponse').html("{{Lang::get('po.alertUOM')}}").show();
                        window.setTimeout(function(){$('#error-msg').hide()},2000);
                        $('.loderholder').hide();
                        $('html, body').animate({scrollTop: '0px'}, 500);
                    }
                }else{
                    $('#ajaxResponse').html("{{Lang::get('po.alertPOQty')}}").show();
                    window.setTimeout(function(){$('#error-msg').hide()},2000);
                    $('.loderholder').hide();
                    $('html, body').animate({scrollTop: '0px'}, 500);
                }
                }else{                    
                }
            }
        });
        checkPOType();
        autosuggest();
        $('#addSkubtn').click(function(){
           var product_id = $('#addproduct_id').val();
           if(product_id!=''){
               if(checkProductAdded(product_id)){
                    var sno_increment = $('#sno_increment').val();
                    var supplier_id = $('#supplier_id').val();
                    var warehouse_id = $('#warehouse_id').val();
                    $('#addSkubtn').attr('disabled',true);
                    $.ajax({
                             headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                             url: '/po/getProductInfo',
                             type: 'POST',
                             data: {product_id:product_id,sno_increment:sno_increment,supplier_id:supplier_id,warehouse_id:warehouse_id,addfrom:'edit_po'},
                             dataType:'JSON',
                             success: function (data) {
                                $('#addSkubtn').attr('disabled',false);
                                if(data.status==200){
                                    $('#product_list').append(data.productList);
                                    checkPOType();
                                    $('#sno_increment').val(data.sno);
                                    $('.close').click();
                                    $('#search_sku').val('');
                                    $('#addproduct_id').val('');
                                    $('#prod_brand').text('');
                                    $('#prod_sku').text('');
                                    $('#prod_mrp').text('');
                                    $(".fixTable").tableHeadFixer({"head" : false, "right" : 2});
                                    calTotalvalues();
                                    snoCount();
                                }else{
                                    $('#error-msg').html(data.message).show();
                                    window.setTimeout(function(){$('#error-msg').hide()},3000);
                                }
                             },
                             error: function (response) {

                             }
                         });
                 }else{
                     $('#error-msg').html('Product is already added.').show();
                     window.setTimeout(function(){$('#error-msg').hide()},2000);
                 }
           }else{
               $('#error-msg').html('Please Add Product').show();
               window.setTimeout(function(){$('#error-msg').hide()},2000);
           }
        });
    $('#closePObtn').click(function(){
        var po_id = $('#po_id').val();
        var po_status_val = $('#po_status_val').val();
        var close_reason = $('#close_reason').val();
        var type= 'cancel';
        if(po_status_val=='87002'){
            type= 'close';
        }
        if(close_reason.trim()!=''){                
            if(po_id!=''){
                if(confirm('Do you want to '+type+' PO?')){
                    $('.close').click();
                    $('.loderholder').show();
                    $.ajax({
                        headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                        url: '/po/closePO',
                        type: 'POST',
                        data: { po_id:po_id,po_status:po_status_val,close_reason:close_reason },
                        dataType:'JSON',
                        success: function (data) {
                            if (data.status == 200) {
                                $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                                window.setTimeout(function(){window.location.href = '/po/details/' + po_id},1000);
                            }
                        },
                        error: function (response) {
                        }
                    });
                }else{
                    return false;
                }
            }else{
                $('#error-msg1').html('Invalid PO ID').show();
                window.setTimeout(function(){$('#error-msg1').hide()},2000);
            }
        }else{
            $('#error-msg1').html('Please Enter Reason').show();
           window.setTimeout(function(){$('#error-msg1').hide()},2000);
        }
    });
    $('#payment_mode').change(function(){
        var payment_mode = parseInt($(this).val());
        $('#paid_through').removeAttr('required');
        $('#payment_type').removeAttr('required');
        $('#paymentdiv').hide();
        $('#paymentduediv').show();
        if(payment_mode==2){
            //$('#paid_through').attr('required','required');
            //$('#payment_type').attr('required','required');
            $('#paymentdiv').show();
            $('#paymentduediv').hide();
        }
    });
    //$("#payment_mode").select2().select2("val", {{}});
    //$("#paid_through").select2().select2("val", '<?php //echo $paid_through?>');
    //$("#payment_type").select2().select2("val", '<?php //echo $payment_type ?>');
    $("#proforma").change(function() {
        var file_data = $('#proforma').prop('files')[0];
        var form_data = new FormData();
        form_data.append('upload_file', file_data);
        $('.loderholder').show();
        $.ajax({
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            url: '/po/uploadpodocs',
            dataType: 'html',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data, 
            mimeType: "multipart/form-data",
            type: 'POST',
            success: function(data){
                data = jQuery.parseJSON(data);
                if(data.status==200){
                    $("#doc_text").append(data.docText);
                    $('.fileinput-filename').text("");
                }else{
                    alert(data.message);
                }
                $('.loderholder').hide();
            },
            error: function (response) {
                alert("Unable to save file");
                $('.loderholder').hide();
            }
        });
    });
    $(document).on('click','.downloadclose',function(){
        var doc_id = $(this).attr('data-doc_id');
        var reference = $(this);
        if(confirm('Do you want to remove?')){
            $.ajax({
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/po/deleteDoc/'+doc_id,
                type: 'POST',
                data: {},
                dataType:'JSON',
                success: function (data) {
                    if (data.status == 200) {
                        reference.closest("div").remove();
                    }
                },
                error: function (response) {
                }
            });
        }else{
            return false;
        }
    });
});
function checkProductQty() {
        var checked = true;
        $("input[name='qty[]']").each(function () {
            var productQty=$(this).val();
            if (productQty=='' || productQty<=0) {
                checked = false;
                return;
            }
        });
        return checked;
    }
    function checkProductUOM() {
        var checked = true;
        $("[name='packsize[]']").each(function () {            
            var packsize=$(this).val();
            if (packsize=='') {
                checked = false;
                return;
            }
        });
        return checked;
    }
    function checkProductMRP() {
        skus='';
        var checked = true;
        $(".unitPrice").each(function () {
            var product_id=$(this).attr('data-product_id');
            var unitprice=parseFloat($(this).val());
            var tax_per=parseFloat($('#taxper'+product_id).val());
            var pre_post_type = $('input[name="pretax['+product_id+']"]:checked').val();
            pre_post_type = (pre_post_type) ? pre_post_type : 0;            
            var elp = unitprice;
            if(pre_post_type==0){
                var elp = unitprice+(unitprice*tax_per/100);
            }
            var mrp=parseFloat($('#mrp'+product_id).val());
            var sku=$('#product_sku'+product_id).val();
            if(elp>mrp){
                checked = false;
                skus = skus+sku+',';
                return;
            }
        });
        skus='<strong>'+skus.replace(/,\s*$/, "")+'</strong>';
        return checked;
    }
    function checkProductTax() {
        skus='';
        var checked = true;
        $(".tax_code").each(function () {
            var product_id = $(this).closest('tr').find('input[name="po_product_id[]"]').val();
            var parent_id = $(this).closest('tr').find('input[name="parent_id[]"]').val();            
            var tax_code=$(this).val();
            var sku=$('#product_sku'+product_id).val();
            if(tax_code == '' && product_id==parent_id){
                checked = false;
                skus = skus+sku+',';
                return;
            }
        });
        skus='<strong>'+skus.replace(/,\s*$/, "")+'</strong>';
        return checked;
    }
    function checkPOType(){
        var po_type = $('#po_type').val();
        if(po_type ==1){
            $('.potypeshow').hide();
        }else{
            $('.potypeshow').show();
        }
    }
    function checkProductAdded(product_id) {
        var checked = true;
        $("input[name='po_product_id[]']").each(function () {
            var productid_exist=$(this).val();
            if (productid_exist == product_id) {
                checked = false;
                return;
            }
        });
        return checked;
    }
    function autosuggest(){
        $( "#search_sku" ).autocomplete({
             source: '/po/getSkus?supplier_id='+$('#supplier_id').val()+'&warehouse_id='+$('#warehouse_id').val(),
             minLength: 2,
             params: { entity_type:$('#supplier_list').val() },
             select: function( event, ui ) {
                 if(ui.item.label=='No Result Found'){
                    event.preventDefault();
                 }
                  $('#addproduct_id').val(ui.item.product_id);
                  $('#prod_brand').text(ui.item.brand);
                  $('#prod_sku').text(ui.item.sku);
                  $('#prod_mrp').text(ui.item.mrp);
             }
         });
    }





    function updateSupplier(){
        var frmData = $('#update_supplier_form').serialize();

        if($('#supp_name').val() == ""){
            alert("Select Supplier!");
            return false;
        }

        $.ajax({
            type: "POST",
            url: '/po/updatesupplier',
            data: frmData,
            success: function (respData)
            {
                var data = respData;
                if(data.status == 1){
                    $('#update_supplier').modal('toggle');
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
                    $(".alert-success").fadeOut(20000);
                    location.reload();
                } else if (data.message == 'inv_error_found'){
                    $("#prcingMismatchModal").modal('toggle');
                    $('.loderholder').hide();
                    $('#priceMismatchData').html(data.data);
                    $('#reason_po_so').html(data.reason);
                    $('#po_so_adjust_message').html(data.adjust_message);
                } else{
                    alert(data.message);
                }
                
            },
            error: function (response) {
                alert("Technical Error!");
                $('#update_supplier').modal('toggle');
            }
        });
    }

    function updateSuppyDc(){
        var frmData = $('#update_supply_dc_form').serialize();

        if($('#supply_dc_name').val() == ""  && <?php echo ($supply_dc_name == "")? 1 : 0;?>){
            alert("Select Supply DC!");
            return false;
        }

        $.ajax({
            type: "POST",
            url: '/po/updatesuppydc',
            data: frmData,
            success: function (respData)
            {
                var data = respData;
                if(data.status == 1){
                    $('#update_supply_dc').modal('toggle');
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
                    $(".alert-success").fadeOut(20000);
                    location.reload();
                }else{
                    alert(data.message);
                }
                
            },
            error: function (response) {
                alert("Technical Error!");
                $('#update_supply_dc').modal('toggle');
            }
        });
    }

    function updateStDc(){
        var frmData = $('#update_st_dc_form').serialize();

        if($('#st_dc_name').val() == ""  && <?php echo ($st_dc_name == "")? 1 : 0;?>){
            alert("Select Stock Transfer Location!");
            return false;
        }

        $.ajax({
            type: "POST",
            url: '/po/updatestdc',
            data: frmData,
            success: function (respData)
            {
                var data = respData;
                if(data.status == 1){
                    $('#update_st_dc').modal('toggle');
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
                    $(".alert-success").fadeOut(20000);
                    location.reload();
                }else{
                    alert(data.message);
                }
                
            },
            error: function (response) {
                alert("Technical Error!");
                $('#update_st_dc').modal('toggle');
            }
        });
    }
    function calTotalvalues(){
        var taxtot = 0;
        $("[id^=taxval]").each(function () {
            taxtot = taxtot + Number($(this).val());            
        });
        $("#tax_total").html($.number(taxtot,2));    

        var tot = 0;

        $("[name='po_totprice[]']").each(function () {
            tot = tot + Number($(this).val());
        });
        $("#grand_total").html($.number(tot,2));

        var subtot = 0;
       
        $('[id^=totalPriceText]').each(function () {
            subtot = subtot + Number($(this).html());
        });
        $("#total_sub_total").html($.number(subtot,2));
    }
    function snoCount(){
        var sno=1;
       $('.snos').each(function () {
        
            $(this).html(sno);
            sno++;
        });
    }


    function updatePoSoCode(){

        var old_po_so_order_code = "{{$po_so_order_code}}";
        var po_id = $("#po_id").val();
        if(confirm("Are you sure to unlink SO from PO?")){
            $.ajax({
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                type: "POST",
                url: '/po/po_so_code_update',
                data: {old_po_so_order_code:old_po_so_order_code,po_so_order_code:"",po_id:po_id},
                success: function (respData)
                {
                    var data = respData;
                    if(data.status == 1){
                        $('#po_so_order_code_form').modal('toggle');
                        $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
                        $(".alert-success").fadeOut(20000);
                        location.reload();
                    }else{
                        alert(data.message);
                    }
                    
                },
                error: function (response) {
                    alert("Technical Error!");
                }
            });
        }
    }


    </script>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
    @stop

