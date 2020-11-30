@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/pr/index">Purchases</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Purchase Returns</li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">

            <div class="portlet-title">
                <div class="caption"> PURCHASE RETURN # {{$productArr[0]->pr_code}}</div>
                <div class="pull-right margin-top-10">
                    <a target="_blank" class="btn green-meadow" href="/pr/printpr/{{$productArr[0]->pr_id}}"><i class="fa fa-print"></i></a>&nbsp;&nbsp;
                    <a class="btn green-meadow" href="/pr/downloadpr/{{$productArr[0]->pr_id}}"><i class="fa fa-download"></i></a>&nbsp;&nbsp;
                   <a class="btn green-meadow" href="/pr/excel/{{$productArr[0]->pr_id}}" data-toggle="tooltip" title="Download PR to Excel"><i class="fa fa-file-excel-o"></i></a>&nbsp;&nbsp;
                    <a href="/pr/edit/{{$productArr[0]->pr_id}}" class="btn green-meadow">Edit PR</a></div>
            </div>

            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs ">
                        <li class="active"><a href="#tab11" data-toggle="tab" aria-expanded="false">Details</a></li>
                        <li class=""><a href="#tab22" data-toggle="tab" aria-expanded="true">Approval History</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab11">
                            <div class="row">
                                <div class="col-md-4">
                                    <h4>Supplier</h4>
                                    <div class="well1 margin-top-10">
                                        <div class="table-scrollable table-scrollable-borderless">
                                            <table class="table">
                                                <tbody>
                                                    <tr height= "10%">
                                                        <td><strong> Name </strong></td>
                                                        <td> {{$supplier->business_legal_name}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top"><strong> Address </strong></td>
                                                        <td valign="top"> {{$supplier->address1}}, {{$supplier->address2}},<br />
                                                            {{$supplier->city}}, {{$supplier->state_name}} - {{$supplier->pincode}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Phone </strong></td>
                                                        <td> {{$userInfo->mobile_no}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Email </strong></td>
                                                        <td> {{$userInfo->email_id}} </td>
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
                                                        <td><strong> GSTIN </strong></td>
                                                        <td>{{$supplier->gstin}}</td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <h4>Dispatch Address</h4>
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
                                                            {{$whDetail->city}}, {{$whDetail->state_name}} - {{$whDetail->pincode}} </td>
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
                                    <h4>PR Details</h4>
                                    <div class="well1 margin-top-10">
                                        <div class="table-scrollable table-scrollable-borderless">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td><strong> PR No </strong></td>
                                                        <td> {{$productArr[0]->pr_code}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> PR Date </strong></td>
                                                        <td> {{Utility::dateFormat($productArr[0]->created_at)}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Created By </strong></td>
                                                        <td> {{$productArr[0]->user_name}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong> Approval Status </strong></td>
                                                        <td> {{$productArr[0]->approval_status_name}} </td>
                                                    </tr>
                                                    @if(isset($prDocs) && count($prDocs)>0)
                                                    <tr>
                                                        <td><strong> Purchase Returns Invoice(Ack) </strong></td>
                                                        <td>
                                                            <div class="downloaddocs">
                                                            @foreach($prDocs as $docs)    
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
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Product Details</h4>
                                    <span style="float:right;font-size: 10px;font-weight: bold;">* All Amounts in (₹) </span>
                                    <table class="table table-striped table-bordered table-advance table-hover table-scrolling" id="sample_2-pd">
                                        <thead>
                                            <tr>
                                                <th>S&nbsp;No</th>
                                                <th>SKU</th>
                                                <th>Product&nbsp;Name</th>
                                                <th>HSN&nbsp;Code</th>
                                                <th>MRP</th>
                                                <th style="text-align:right">Base&nbsp;Rate</th>
                                                <th>Qty</th>
                                                <th style="text-align:right">Taxable&nbsp;Value</th>
                                                <th style="text-align:right">Tax%</th>
                                                <th style="text-align:right">Tax&nbsp;Value</th>
                                                <?php /* <th width="5%">Discount</th> */ ?>
                                                <th style="text-align:right">Total</th>    
                                            </tr>
                                        </thead>

                                        <tbody>
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
                                            @foreach($productArr as $product)
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
                                                <td align="center">{{$sno++}}</td>
                                                <td>{{$product->sku}}</td>
                                                <td>{{$product->product_name}}</td>
                                                <td>{{$product->hsn_code}}</td>
                                                <td align="left" valign="middle">{{number_format($product->mrp,2)}}</td>
                                                <td align="right">{{number_format($basePrice,2)}}</td>
                                                <td align="center">{{$qty}} {{$uom}} {{($uom!='Eaches') ? '('.$qty*$no_of_eaches.' Eaches)' : ''}}<br/>
                                                    <span style="font-size:10px;font-weight: bold;">
                                                        @if($soh_qty >0)SOH Qty: {{$soh_qty}}<br> @endif
                                                        @if($dit_qty >0)DIT Qty: {{$dit_qty}}<br> @endif
                                                        @if($dnd_qty >0)DND Qty: {{$dnd_qty}}@endif
                                                    </span>
                                                </td>
                                                <td align="right">{{number_format($subTotal, 2)}}</td>
                                                <td align="right">{{empty($product->tax_type) ? '' : $product->tax_type.' @ '}}{{(float)$product->tax_per}}</td>
                                                <td align="right">{{$product->tax_total}}</td>
                                                <?php /* <td>{{$product->discount_amt}}</td> */ ?>
                                                <td align="right">{{$product->total}}</td>
                                            </tr>
                                            <?php
                                            $sumOfTaxtotal = $sumOfTaxtotal + $product->tax_total;
                                            $sumOfSubtotal = $sumOfSubtotal + $subTotal;
                                            $sumOfTotalValue = $sumOfTotalValue + $product->total;
                                            $totQty = $totQty + $product->qty;
                                            //$totdis = $totdis + $product->discount_amt;
                                            ?>
                                            @endforeach
                                        </tbody>
                                        <tr>
                                            <td colspan="7" align="right"><strong>Total</strong></td>
                                            <td align="right">{{number_format($sumOfSubtotal, 2)}}</td>
                                            <td align="right"></td>
                                            <td align="right">{{number_format($sumOfTaxtotal, 2)}}</td>
                                            <?php /* <td>{{number_format($totdis,2)}}</td> */ ?>
                                            <td align="right">{{number_format($sumOfTotalValue,2)}}</td>
                                        </tr>
                                    </table>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 hidden-sm">
                                            @include('PurchaseOrder::Form.approvalForm')
                                        </div>
                                        <div class="col-md-4">
                                            <div class="well">
                                                <table class="table">
                                                    <tr>
                                                        <th width="5%">Tax Type</th>
                                                        <th width="5%">Tax Amount</th>
                                                    </tr>
                                                    @foreach($taxBreakup as $tax)
                                                    @if($tax['name']!='' && $tax['tax_value']>0)
                                                    <tr>
                                                        <td>{{$tax['name']}}</td>
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
                                                        <td width="60%" align="right"><strong>Total Base Price: </strong></td>
                                                        <td align="right">{{number_format($sumOfSubtotal, 3)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td width="60%" align="right"><strong>Total Tax: </strong></td>
                                                        <td align="right">{{number_format($sumOfTaxtotal, 3)}}</td>
                                                    </tr>
                                                    <?php /* <tr>
                                                      <td width="60%" align="right"><strong>Discount On Items: </strong></td>
                                                      <td align="right">Rs. {{number_format($totdis, 3)}}</td>
                                                      </tr>
                                                      <tr>
                                                      <td width="60%" align="right"><strong>Discount On Bill: </strong></td>
                                                      <td align="right">Rs. {{number_format($product->bill_discount_amt, 3)}}</td>
                                                      </tr> */ ?>                                     
                                                    <tr>
                                                        <td width="60%" align="right"><strong>Grand Total: </strong></td>
                                                        <td align="right">{{number_format($product->pr_grand_total, 3)}}</td>
                                                    </tr>
                                                </table>                                        
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4>Remarks</h4>
                                            <p>{{$productArr[0]->pr_remarks}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab22">
                            @include('PurchaseOrder::Form.approvalHistory')
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop
@section('style')
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/image-box/css/style.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
    .loderholder img{ position: absolute; top:50%;left:50%; }
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        border-top: 0px !important;
    }
    .favfont i{font-size:18px !important; color:#5b9bd1 !important;}
    .tabbable-line > .nav-tabs > li > a > i {
        color: #5b9bd1 !important;
    }
    .well1 {
        height:320px;
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
</style>
@stop
@section('userscript')
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/approvalscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/image-box/js/jquery.imagebox.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(".downloaddocs").imageBox();
</script>
@stop