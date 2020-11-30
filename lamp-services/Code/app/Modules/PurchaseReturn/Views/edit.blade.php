@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/pr/index">Purchases</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Purchase Orders</li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <?php
            $prStatus = $productArr[0]->pr_status;
            ?>
            <div class="portlet-title">
                <div class="caption"> PURCHASE RETURN # {{$productArr[0]->pr_code}}</div>
            </div>
            <div class="portlet-body">
                <div id="ajaxResponse" style="display:none;" class="alert alert-danger"></div>
                <div class="tabbable-line">
                    <div class="tab-content">
                        @if($prStatus==103001)
                        <form id="pr_form" method="POST" action="/pr/updatePr">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="POST">
                            <input type="hidden" id="pr_id" name="pr_id" value="{{$productArr[0]->pr_id}}"/>
                            @if(isset($productArr[0]->sr_invoice_code) && !empty($productArr[0]->sr_invoice_code))
                                <input type="hidden" id="sr_invoice_code" name="sr_invoice_code" value="{{$productArr[0]->sr_invoice_code}}"/>
                            @endif
                            <input type="hidden" name="supplier_id" id="supplier_id" value="{{$productArr[0]->legal_entity_id}}"/>
                            <input type="hidden" name="warehouse_id" id="warehouse_id" value="{{$productArr[0]->le_wh_id}}"/>
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
                                                    @if(!empty($supplier->tin_number))
                                                    <tr>
                                                        <td><strong> TIN </strong></td>
                                                        <td>{{(!empty($supplier->tin_number) ? $supplier->tin_number : 'NA')}}</td>
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
                                                        <td><strong> A/c No. </strong></td>
                                                        <td>{{(!empty($supplier->sup_account_no) ? $supplier->sup_account_no : 'NA')}}</td>
                                                    </tr>
                                                    @endif
                                                    @if(!empty($supplier->sup_account_name))
                                                    <tr>
                                                        <td><strong> A/c Name. </strong></td>
                                                        <td>{{(!empty($supplier->sup_account_name) ? $supplier->sup_account_name : 'NA')}}</td>
                                                    </tr>
                                                    @endif
                                                    @if(!empty($supplier->sup_ifsc_code))
                                                    <tr>
                                                        <td><strong> IFSC Code </strong></td>
                                                        <td>{{(!empty($supplier->sup_ifsc_code) ? $supplier->sup_ifsc_code : 'NA')}}</td>
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
                                                        <td valign="top">{{$whDetail->address1}}, {{$whDetail->address2}},<br />
                                                            {{$whDetail->city}}, {{$whDetail->state_name}}, {{$whDetail->country_name}} - {{$whDetail->pincode}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Phone </strong></td>
                                                        <td>{{isset($whDetail->phone_no) ? $whDetail->phone_no : 'NA'}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Email </strong></td>
                                                        <td>{{isset($whDetail->email) ? $whDetail->email : 'NA'}}</td>
                                                    </tr>
                                                    <tr>
							<td><strong> Contact Person </strong></td>
							<td>{{isset($whDetail->contact_name) ? $whDetail->contact_name : 'NA'}}</td>
						</tr>
                                                    @if(isset($productArr[0]->sr_invoice_code) && !empty($productArr[0]->sr_invoice_code))
                                                        <tr>
                                                            <td><strong>Sales Return Invoice No</strong></td>
                                                            <td>{{$productArr[0]->sr_invoice_code}}</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <h4>P.O Details</h4>
                                    <div class="well1 margin-top-10">
                                        <div class="table-scrollable table-scrollable-borderless">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td><strong> PR Number </strong></td>
                                                        <td> {{$productArr[0]->pr_code}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Created By </strong></td>
                                                        <td> {{$productArr[0]->user_name}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Status </strong></td>
                                                        <td> {{$pr_status}} </td>
                                                    </tr>
                                                    @if($approvedStatus!='')
                                                    <tr>
                                                        <td><strong> Approval Status </strong></td>
                                                        <td> {{$approvedStatus}} </td>
                                                    </tr>
                                                    @endif                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Purchase Return Ack</strong></label>
                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                            <div>
                                                <span class="btn default btn-file btn green-meadow">
                                                    <span class="fileinput-new">Choose File</span>
                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Change File</span>
                                                    <input class="form-control" type="file" id="returnack" name="returnack" placeholder="Proof of Document">
                                                </span>
                                                <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                <div class="thumbnail">
                                                    <div id="doc_text">
                                                        @if(isset($prDocs) && count($prDocs)>0)
                                                            @foreach($prDocs as $docs)
                                                            <div>
                                                                <span><i class="fa fa-close downloadclose" data-doc_id="{{$docs->doc_id}}"></i></span>
                                                                <a href="{{$docs->file_path}}" class="closedownload"><i class="fa fa-download"></i></a>
                                                            </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 text-right">
                                    <div class="form-group">
                                        <br><br>
                                        <button type="button" id="addskuprpupbtn" href="#basicvalCodeModal3" data-toggle="modal" class="btn green-meadow">Add SKU </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <span style="float:right;font-size: 10px;">* All Amounts in (₹) </span>
                                    <h4>Product Details</h4>
                                    <div class="parent">
                                        <table class="table table-striped table-bordered table-advance table-hover fixTable" id="product_list" style="white-space:nowrap; width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>SNo</th>
                                                    <th>SKU</th>
                                                    <th>Product&nbsp;Name</th>
                                                    <th title="Return from SOH">SOH Qty</th>
                                                    <th title="Return from Damaged Qty">DIT Qty</th>
                                                    <th title="Return from Missing Qty">DND Qty</th>
                                                    <?php /* <th>Free Qty</th> */ ?>
                                                    <th>MRP</th>
                                                    <th>{{Lang::get('headings.LP')}}</th>
                                                    <th class="">Price</th>
                                                    <th class="">Sub&nbsp;Total</th>
                                                    <th class="">Tax%</th>
                                                    <th class="">Tax</th>
                                                   <?php /* <th class="">Discount</th>
                                                    <th class="">Disc.Amount</th> */ ?>
                                                    <th class="" style="position:absolute;right: 60px;">Total</th>
                                                    <th class="" style="width: 60px !important;">Actions</th>
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
                                                ?>
                                                @foreach($productArr as $product)
                                                <?php
                                                #echo '<pre/>';print_r($product);die;
                                                $product_id = $product->product_id;
                                                $sumTax = 0;

                                                $qty = ($product->qty != '') ? $product->qty : 0;
                                                $dit_qty = ($product->dit_qty != '') ? $product->dit_qty : 0;
                                                $dnd_qty = ($product->dnd_qty != '') ? $product->dnd_qty : 0;
                                                $free_qty = ($product->free_qty != '') ? $product->free_qty : 0;

                                                $base_price = (isset($product->price) && $product->price != '') ? $product->price : 0;
                                                
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
                                                
                                                $no_of_eaches = ($product->no_of_eaches == 0 || $product->no_of_eaches == '') ? 1 : $product->no_of_eaches;
                                                //$unit_disc = $discAmt/($qty * $no_of_eaches);
                                                //$current_elp = ($current_elp-$unit_disc);
                                                $taxper = (isset($product->tax_per))?$product->tax_per:0;
                                                $taxname = (isset($product->tax_name))?$product->tax_name:0;
                                                $taxAmt = (isset($product->tax_amt))?$product->tax_amt:0;
                                                $taxTotal = (isset($product->tax_total))?$product->tax_total:0;
                                                $taxincludecheck = ($isTaxInclude==1)?'checked':'';
                                                $hsn_code = '';
                                                $tax_data = '';
                                                $tax_code = '';
                                                if(isset($taxArr[$product_id]) && is_array($taxArr[$product_id])){
                                                    foreach($taxArr[$product_id] as $tax){
                                                        $sumTax = $sumTax + $tax['Tax Percentage'];
                                                        //$taxAmt = (isset($taxTypeAmt[''.$tax['Tax Percentage'].'']))? $taxTypeAmt[''.$tax['Tax Percentage'].'']:0;
                                                        //$taxTypeAmt[''.$tax['Tax Percentage'].''] = $taxAmt+$product->unit_price * $product->qty;
                                                        $taxname = $tax['Tax Type'];
                                                        $taxper = $tax['Tax Percentage'];
                                                        if($tax_code==''){
                                                            $tax_code = isset($tax['Tax Code'])?$tax['Tax Code']:'';
                                                        }
                                                        if($hsn_code==''){
                                                            $hsn_code = isset($tax['HSN_Code'])?$tax['HSN_Code']:'';
                                                        }
                                                    }
                                                    $tax_data = base64_encode(json_encode($taxArr[$product_id]));
                                                }
                                                ?>
                                                <tr class="odd gradeX">
                                                     <td align="center">{{$sno++}}</td>
                                                    <td>{{$product->sku}}<input type="hidden" name="pr_product_id[]" value="{{$product_id}}">
                                                        <input type="hidden" id="product_sku{{$product_id}}" value="{{$product->sku}}">
                                                        <input type="hidden" name="parent_id[]" value="{{$product->parent_id}}"></td>
                                                    <td>{{$product->product_name}}</td>
                                                    <td align="center">
                                                        <div style="width:90px">
                                                            <div style="float:left">
                                                                <input class="form-control" id="soh_qty{{$product_id}}" min="0" type="number" size="3" style="width:85px;" value="{{$product->qty}}" name="soh_qty[]">
                                                                <input class="form-control" type="hidden" id="packsize{{$product_id}}" min="1" value="1">
                                                            </div>
                                                          <?php /* <div style="float:right">
                                                                <select class="form-control packsize{{$product_id}}" name="packsize[]" required="required" style="width:100px;"><?php echo $uomOptions; ?></select>
                                                            </div> */ ?>
                                                        </div>
                                                    </td>
                                                   <?php /*<td> 
                                                        <div style="width:175px">
                                                            <div style="float:left">
                                                                <input class="form-control" id="freeqty{{$product_id}}" min="0" type="number" size="3" style="width:70px;" value="{{$free_qty}}" name="freeqty[]">
                                                            </div>
                                                            <div style="float:right">
                                                                <select class="form-control freepacksize{{$product_id}}" name="freepacksize[]" required="required" style="width:100px;"><?php echo $freeUOMOptions; ?></select>
                                                            </div>
                                                        </div>                    
                                                    </td> */ ?>
                                                    <td>
                                                        <div style="width:90px">
                                                            <div style="float:left"> 
                                                            <input class="form-control" size="3" type="number" id="dit_qty{{$product_id}}" min="0" value="{{$product->dit_qty}}" name="dit_qty[]" style=" width:85px">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div style="width:90px">
                                                            <div style="float:left"> 
                                                            <input class="form-control" size="3" type="number" id="dnd_qty{{$product_id}}" min="0" value="{{$product->dnd_qty}}" name="dnd_qty[]" style=" width:85px">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td align="center">{{number_format($product->mrp, 4)}}</td>
                                                    <td align="center"><span id="curelptext{{$product_id}}">{{number_format($current_elp, 3)}}</span></td>
                                                    <td class="">
                                                        <div style="width:170px">
                                                            <div style="float:left">
                                                                <input class="form-control prbaseprice" min="0" step="any" id="baseprice{{$product_id}}" style="width:100px;" name="pr_baseprice[]" type="number" value="{{$product->price}}">
                                                            </div>
                                                            <div style="float:left">
                                                                <input class="pretax pretax{{$product_id}}" data-id="{{$product_id}}" {{$taxincludecheck}} name="pretax[{{$product_id}}]" type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                                                            </div>
                                                            <div style="float:left"><span style="margin-top:8px; font-size:9px;">Incl.Tax </span>  </div>
                                                        </div>
                                                        <input id="currsoh{{$product_id}}" name="currsoh[{{$product_id}}]" type="hidden" value="{{$product->soh}}">
                                                        <input id="currdit{{$product_id}}" name="currdit[{{$product_id}}]" type="hidden" value="{{$product->inv_dit_qty}}">
                                                        <input id="currdnd{{$product_id}}" name="currdnd[{{$product_id}}]" type="hidden" value="{{$product->inv_dnd_qty}}">
                                                        <input id="taxname{{$product_id}}" name="pr_taxname[{{$product_id}}]" type="hidden" value="{{$taxname}}">
                                                        <input id="taxdata{{$product_id}}" name="pr_taxdata[{{$product_id}}]" type="hidden" value="{{$tax_data}}">
                                                        <input id="hsn_code{{$product_id}}" name="hsn_code[{{$product_id}}]" type="hidden" class="hsn_code" value="{{$hsn_code}}">
                                                        <input id="tax_code{{$product_id}}" name="tax_code[{{$product_id}}]" type="hidden" class="tax_code" value="{{$tax_code}}">
                                                        <input id="mrp{{$product_id}}" type="hidden" value="{{$product->mrp}}">
                                                        <input id="unit_price{{$product_id}}" name="unit_price[{{$product_id}}]" class="unitPrice" data-product_id="{{$product_id}}" type="hidden" value="{{$product->unit_price}}">
                                                        <input id="taxper{{$product_id}}" name="pr_taxper[{{$product_id}}]" type="hidden" value="{{$sumTax}}">
                                                        <input name="pr_taxvalue[{{$product_id}}]" id="taxval{{$product_id}}" type="hidden" value="{{$taxTotal}}">
                                                      <?php /*<input id="item_discount_amt{{$product_id}}'" name="item_discount_amt[{{$product_id}}]" type="hidden" value="{{$product->discount_amt}}"> */ ?>
                                                    </td>
                                                    <td class="" align="right"><span id="totalPriceText{{$product_id}}">{{number_format(($product->sub_total), 4)}}</span></td>
                                                    <td class="" align="center">
                                                        {{$taxname.'@'.(float)$taxper}}
                                                    </td>
                                                    <td class="" align="right"><span id="taxtext{{$product_id}}">{{number_format($taxAmt, 4)}}</span></td>                                                    
                                                   <?php /* 
                                                    <td class="prtypeshow" align="right">
                                                        <div style="width:170px">
                                                        <div style="float:left"><input class="item_disc_tax_type" <?php if($product->discount_inc_tax==1) echo 'checked'; ?> id="item_disc_tax_type{{$product_id}}" name="item_disc_tax_type[]" type="checkbox" value="1">
                                                        <span style="margin-top:8px; font-size:9px;"><strong>Incl.Tax</strong></span></div><br/>
                                                        <div style="float:left"><input class="form-control item_discount" min="0" id="item_discount{{$product_id}}" style="width:100px;" name="item_discount[]" type="number" value="{{$product->discount}}"></div>
                                                        <div style="float:left"><input class="item_discount_type" <?php if($product->discount_type==1) echo 'checked'; ?> id="item_discount_type{{$product_id}}" name="item_discount_type[]" type="checkbox" value="1" style="margin:7px 10px 0px 10px;"></div>
                                                        <div style="float:left"><span style="margin-top:8px; font-size:9px;"><strong>%</strong></span>  </div>
                                                        </div>
                                                    </td>
                                                    <td class="prtypeshow" align="right">                                                        
                                                        <span  id="item_discount_text{{$product_id}}">{{$product->discount_amt}}</span>
                                                    </td>
                                                    */ ?>
                                                    <td class="" align="right" style="background:#fbfcfd !important;position:absolute;right: 60px;">
                                                        <input class="form-control prtotprice" min="0" step="any" id="totprice{{$product_id}}" style="width:100px;" name="pr_totprice[]" type="number" value="{{($product->total)}}">
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
                            <?php /*<div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Discount</strong></label>
                                        <div style="float:left"><input class="form-control" min="0" id="discount" style="width:100px;" name="bill_discount" type="number" value="{{$productArr[0]->bill_discount}}"></div>
                                        <div style="float:left"><input class="bill_discount_type" <?php if(isset($productArr[0]->bill_discount_type)&&$productArr[0]->bill_discount_type==1){ echo 'checked'; } ?> name="bill_discount_type" type="checkbox" value="1"  style="margin:7px 6px 0px 10px;"></div>
                                        <div style="float:left"><span style="margin-top:8px; font-size:9px;"><strong>%</strong></span>  </div>
                                    </div>
                                </div>                                                
                            </div>
                             */ ?>
                            <div class="row">
                            <div class="col-md-4">
                                <h4>Remarks</h4>
                                <textarea class="form-control" name="pr_remarks" cols="60" id="pr_remarks" rows="2">{{($productArr[0]->pr_remarks)}}</textarea>
                                </div>
                            </div>
                            <div class="row" style="margin-top:10px;">
                                <hr />
                                <div id="deleted_products"></div>
                                <div class="col-md-12 text-center">
                                    <input type="hidden" name="" id="sno_increment" value="{{$sno}}">
                                    <button type="submit" class="btn green-meadow" name="Save" id="save">Update</button>
                                    <a class="btn green-meadow" href="/pr/index">Cancel</a>
                                </div>
                            </div>
                        </form>
                        
                        @else                    
                        <div class="col-md-12 alert alert-danger">
                                PR Can't Be Edit
                            </div>
                        @endif                        
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
                                <label class="control-label">SKU <span class="required">*</span></label>
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
    @stop

@section('style')

<style type="text/css">
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
            height:335px;
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
$(document).ready(function () {
    $(".fixTable").tableHeadFixer({"head" : false, "right" : 2});
    $('#addproduct_id').val('');
    $('#search_sku').val('');

    var basePrice = 0;
    var totalPrice = 0;
    var qty = 0;
    var soh_qty = 0;
    var dit_qty = 0;
    var dnd_qty = 0;
    var noofeach = 0;
    var freeqty =0;
    var freenoofeach = 0;
    var taxper = 0;
    var taxAmt = 0;
    var totalAmt = 0;
    var pre_post_type = 0;    
    var totfreeqty = 0;
    var totprqty = 0;
    var qtycalculate = 0;
    var eachprice =0; 
    var current_elp=0;
    var price =0;
    var item_discount =0;
    var item_discount_type =0;
    var item_disc_tax_type =0;
    var item_discAmt = 0;
    var subTotal = 0;
    $(document).on('change','.pretax,.prbaseprice,input[name="soh_qty[]"],input[name="dit_qty[]"],input[name="dnd_qty[]"]',function(){ //,[name="item_discount[]"],[name="item_discount_type[]"],[name="item_disc_tax_type[]"],,input[name="freeqty[]"],[name="freepacksize[]"]
        var product_id = $(this).closest('tr').find('input[name="pr_product_id[]"]').val();
        console.log(product_id);
        readValues(product_id);
        calcTotal(product_id);
    });
    $(document).on('change','[name="packsize[]"]',function(){
        var product_id = $(this).closest('tr').find('input[name="pr_product_id[]"]').val();
        var noofeach = parseInt($('.packsize'+product_id).find(':selected').attr('data-noofeach'));
        var unit_price = parseFloat($('#unit_price'+product_id).val());
        console.log('unit='+unit_price+'==eachesss='+noofeach);
        $('#baseprice'+product_id).val(unit_price*noofeach);
        readValues(product_id);
        calcTotal(product_id);
    });
    $(document).on('change','[name="pr_totprice[]"]',function(){
        var product_id = $(this).closest('tr').find('input[name="pr_product_id[]"]').val();
        $('.pretax'+product_id).prop('checked',true);
        readValues(product_id);       
        calcPrice(product_id);
    });
    function readValues(product_id){
        basePrice = parseFloat($('#baseprice'+product_id).val());
        totalPrice = $('#totprice'+product_id).val();
        //qty = $('#qty'+product_id).val();
        soh_qty = $('#soh_qty'+product_id).val();
        dit_qty = $('#dit_qty'+product_id).val();
        dnd_qty = $('#dnd_qty'+product_id).val();
        soh_qty = (soh_qty!='' && soh_qty>=0)?soh_qty:0;
        dit_qty = (dit_qty!='' && dit_qty>=0)?dit_qty:0;
        dnd_qty = (dnd_qty!='' && dnd_qty>=0)?dnd_qty:0;
        qty = parseInt(soh_qty)+parseInt(dit_qty)+parseInt(dnd_qty);
        noofeach = 1;//parseInt($('.packsize'+product_id).find(':selected').attr('data-noofeach'));
        if(!noofeach && isNaN(noofeach)){
            noofeach = 0;
        }
        freeqty = 0;//$('#freeqty'+product_id).val();
        freenoofeach =0; //parseInt($('.freepacksize'+product_id).find(':selected').attr('data-noofeach'));
        if(!freenoofeach && isNaN(freenoofeach)){
            freenoofeach = 0;
        }        
        taxper = parseFloat($('#taxper'+product_id).val());
        console.log('taxper==='+taxper);
        pre_post_type = $('input[name="pretax['+product_id+']"]:checked').val();
        totfreeqty = parseInt(freeqty*freenoofeach);
        totprqty = parseInt(qty * noofeach);
        qtycalculate = totprqty - totfreeqty;
        eachprice =parseFloat(basePrice /noofeach); 
        price =parseFloat(eachprice * qtycalculate);
        
      /*  item_discount =$('#item_discount'+product_id).val();
        item_discount_type =$('#item_discount_type'+product_id+':checked').val();
        item_discount_type = (item_discount_type) ? item_discount_type : 0;
        item_disc_tax_type =$('#item_disc_tax_type'+product_id+':checked').val();
        item_disc_tax_type = (item_disc_tax_type) ? item_disc_tax_type : 0;
        console.log('disc=='+item_discount+'=disctype==='+item_discount_type+'taxtype=='+item_disc_tax_type);*/
    }
    function calcTotal(product_id){
        console.log('totqty===='+totprqty+'freeqty==='+freeqty+'freeeaches=='+freenoofeach);
        if(totprqty>=totfreeqty){
            totalAmt =(eachprice*qtycalculate);
            console.log(product_id+'==price'+price+'==taxper'+taxper+'===Each=='+eachprice);
            pre_post_type = (pre_post_type) ? pre_post_type : 0;            
            if(pre_post_type==0){
                taxAmt = parseFloat((totalAmt*taxper)/100);
                subTotal = totalAmt;
                totalAmt = parseFloat(totalAmt+taxAmt);
                current_elp = eachprice+((eachprice*taxper)/100);
            }else{
                var price_excltax = parseFloat(totalAmt/(1+((taxper*1)/100)));
                subTotal = price_excltax;
                taxAmt = parseFloat(totalAmt-price_excltax);
                totalAmt = parseFloat(totalAmt);
                current_elp = eachprice;
            }
           // item_discAmt = calcItemDiscount(product_id);
           // totalAmt = totalAmt - item_discAmt;
            $('#unit_price'+product_id).val(eachprice);
            $('#totalPriceText'+product_id).text($.number(price,5));
            $('#taxtext'+product_id).text($.number(taxAmt,5));
            $('#curelptext'+product_id).text($.number(current_elp,5));
            $('#taxval'+product_id).val(taxAmt);
            $('#totprice'+product_id).val(totalAmt);
            $('#totalval'+product_id).text($.number(totalAmt,5));            
            console.log(product_id+'==TotT'+taxAmt+'==TotA'+totalAmt+'==='+pre_post_type);
            return false;
        }else{
            alert('Free Qty should not be morethan pr Qty');
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
        qtycalculate = totprqty - totfreeqty;
        eachprice =parseFloat((totalPrice / qtycalculate));
        var price =parseFloat(eachprice*noofeach);
        subTotal = price_excltax;
       // item_discAmt = calcItemDiscount(product_id);
        $('#unit_price'+product_id).val(eachprice);
        $('#curelptext'+product_id).text($.number(eachprice,5));
        $('#totalPriceText'+product_id).text($.number(price_excltax,5));
        $('#taxtext'+product_id).text($.number(taxAmt,5));
        $('#taxval'+product_id).val(taxAmt);
        $('#baseprice'+product_id).val(price).trigger('change');
        console.log(product_id+'==TotT'+taxAmt+'==TotA'+totalAmt+'==='+pre_post_type);
        return false;
    }    
   /* function calcItemDiscount(product_id){
        if(item_discount_type==1){
            if(item_disc_tax_type==1){
                subTotal = subTotal+taxAmt;
            }
            item_discAmt = (subTotal*item_discount)/100;
        }else{
            item_discAmt = item_discount;
        }
        if(item_discAmt>totalAmt){
            alert('Discount amount should not be more than total');
            $('#item_discount'+product_id).val(0);
            readValues(product_id);
            calcTotal(product_id);
            return false;
        }
        $('[name="item_discount_amt['+product_id+']').val(item_discAmt);
        $('#item_discount_text'+product_id).text($.number(item_discAmt,5));
        return item_discAmt;
    } */
    $(document).on('click','.delete_product',function(){
        var product_id = $(this).attr('data-id');
        var reference = $(this);
        var pr_id = $('#pr_id').val();
        if(confirm('Do you want to remove item?')){
            var newproductadd = reference.closest('tr').find('input[name="newproductadd[]"]').val();
            if(product_id!=newproductadd){
                $('#deleted_products').append('<input type="hidden" name="delete_product[]" value="'+product_id+'">');
            }
            reference.closest('tr').remove();
            deleteChildProduct(product_id);            
        }
    });
    function deleteChildProduct(product_id) {
        $("input[name='pr_product_id[]']").each(function () {
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
    $('#pr_form').validate({
            rules: {
                pr_id: {
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
                var form = $('#pr_form');
                if(checkProductQty()){
                    if(checkProductInv()){
                        if(checkProductMRP()){
                            $('.loderholder').show(); 
                            $.post(form.attr('action'), form.serialize(), function (data) {
                                data = jQuery.parseJSON(data);
                                 if (data.status == 200) {
                                     $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                                     $('html, body').animate({scrollTop: '0px'}, 500);
                                     window.setTimeout(function(){window.location.href = '/pr/details/' + data.pr_id},1000);
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
                        $('#ajaxResponse').html("SOH/DIT/DND Qty should not be more than current value for "+skus).show();
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
            }
        });
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
                             url: '/pr/getProductInfo',
                             type: 'POST',
                             data: {product_id:product_id,sno_increment:sno_increment,supplier_id:supplier_id,warehouse_id:warehouse_id,addfrom:'edit_pr'},
                             dataType:'JSON',
                             success: function (data) {
                                $('#addSkubtn').attr('disabled',false);
                                if(data.status==200){
                                    $('#product_list').append(data.productList);
                                    $('#sno_increment').val(data.sno);
                                    $('.close').click();
                                    $('#search_sku').val('');
                                    $('#addproduct_id').val('');
                                    $('#prod_brand').text('');
                                    $('#prod_sku').text('');
                                    $('#prod_mrp').text('');
                                    $(".fixTable").tableHeadFixer({"head" : false, "right" : 2});
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
});
    function checkProductQty() {
        skus='';
        var checked = true;
        $("input[name='soh_qty[]']").each(function () {
            var product_id = $(this).closest('tr').find('input[name="pr_product_id[]"]').val();
            var soh_qty = $(this).val();
            var dit_qty = $('#dit_qty'+product_id).val();
            var dnd_qty = $('#dnd_qty'+product_id).val();
            var productQty = parseInt(soh_qty)+parseInt(dit_qty)+parseInt(dnd_qty);
            if (productQty == '' || productQty <= 0) {
                var sku=$('#product_sku'+product_id).val();
                checked = false;
                skus = skus+sku+',';
                return;
            }
        });
        skus='<strong>'+skus.replace(/,\s*$/, "")+'</strong>';
        return checked;
    }
        function checkProductInv() {
        skus='';
        var checked = true;
        $("input[name='soh_qty[]']").each(function () {
            var product_id = $(this).closest('tr').find('input[name="pr_product_id[]"]').val();
            var soh_qty = $(this).val();
            var dit_qty = $('#dit_qty'+product_id).val();
            var dnd_qty = $('#dnd_qty'+product_id).val();
            
            var currsoh = parseInt($('#currsoh'+product_id).val());
            var currdit = parseInt($('#currdit'+product_id).val());
            var currdnd = parseInt($('#currdnd'+product_id).val());
            var sku=$('#product_sku'+product_id).val();
            //alert('cursoh='+currsoh+'=soh_qty='+soh_qty+'=currdit='+currdit+'=dit_qty='+dit_qty+'=currdnd='+currdnd+'=dnd_qty='+dnd_qty);
            if((soh_qty>currsoh)||(dit_qty>currdit)||(dnd_qty>currdnd)){
                checked = false;
                skus = skus+sku+',';
                return;
            }
        });
        skus='<strong>'+skus.replace(/,\s*$/, "")+'</strong>';
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
    function checkProductAdded(product_id) {
        var checked = true;
        $("input[name='pr_product_id[]']").each(function () {
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

     $("#returnack").change(function() {
        var file_data = $('#returnack').prop('files')[0];
        var form_data = new FormData();
        form_data.append('upload_file', file_data);
        $('.loderholder').show();
        $.ajax({
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            url: '/pr/uploadpodocs',
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
                url: '/pr/deleteDoc/'+doc_id,
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

    </script>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
    @stop

