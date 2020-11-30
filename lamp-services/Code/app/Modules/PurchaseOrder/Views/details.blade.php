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
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption pur-break"> Purchase Order # {{$productArr[0]->po_code}}</div>
                <div class="pull-right margin-top-10">
                    @if(isset($featureAccess['printFeature']) && $featureAccess['printFeature'])
                    <a target="_blank" class="btn green-meadow" href="/po/printpo/{{$productArr[0]->po_id}}" data-toggle="tooltip" title="Print PO"><i class="fa fa-print"></i></a>&nbsp;&nbsp;
                    @if(isset($productArr[0]->stock_transfer) && $productArr[0]->stock_transfer == 1)
                        <a target="_blank" class="btn green-meadow" href="/po/printpo/{{$productArr[0]->po_id}}/1" data-toggle="tooltip" title="Print Stock Transfer Details"><i class="fa fa-exchange"></i></a>&nbsp;&nbsp;
                    @endif
                    @endif
                    @if(isset($featureAccess['downloadFeature']) && $featureAccess['downloadFeature'])
                    <a class="btn green-meadow" href="/po/download/{{$productArr[0]->po_id}}" data-toggle="tooltip" title="Download PO"><i class="fa fa-download"></i></a>&nbsp;&nbsp;
                    <a class="btn green-meadow" href="/po/excel/{{$productArr[0]->po_id}}" data-toggle="tooltip" title="Download PO to Excel"><i class="fa fa-file-excel-o"></i></a>&nbsp;&nbsp;
                    @endif
                    @if($productArr[0]->po_status == '87001' && $productArr[0]->approval_status!='57117' && isset($featureAccess['editFeature']) && $featureAccess['editFeature'])
                    <a class="btn green-meadow" href="/po/edit/{{$productArr[0]->po_id}}" id="updatepo">Update PO</a>&nbsp;&nbsp;
                    @endif
                    @if(isset($featureAccess['closeFeature']) && $featureAccess['closeFeature'] && ($productArr[0]->po_status=='87001'||$productArr[0]->po_status=='87005') && $productArr[0]->is_closed=='0')
                        <?php
                    if(isset($productArr[0]->po_status) && $productArr[0]->po_status=='87005'){
                        $labelName = 'Close PO';
                    }else if(isset($productArr[0]->po_status) && $productArr[0]->po_status=='87001'){
                        $labelName = 'Cancel PO';
                    }
                    ?>
                    <?php /* <button type="button" class="btn green-meadow" href="#closePOModel" data-toggle="modal" title="{{$labelName}}">{{$labelName}}</button> */?>
                    @endif
                    @if(isset($featureAccess['spiltFeature']) && $featureAccess['spiltFeature']  && $productArr[0]->poparentid=='' && !in_array($productArr[0]->le_wh_id,$notallowsplitdcs))
                        <button id="split_po" class="btn green-meadow" data-url="/po/splitpo/{{$productArr[0]->po_id}}" title="Split PO">Split PO</button>&nbsp;&nbsp;
                    @endif

                    @if($featureAccess['createOrderFeature'] == true)
                        @if( isset($orderId) && $orderId != '' && $orderId > 0)
                        <!-- If the order has been created, then we will see the link -->
                            <a  target='_blank' id="view_order" class="btn green-meadow" href="/salesorders/detail/{{$orderId}}" title="View PO Order">View Order</a>&nbsp;&nbsp;
                        @else
                        <!-- If the order hasn`t created, then we will see the link -->
                            @if(!in_array($productArr[0]->le_wh_id,$notallowsplitdcs))
                                <button id="create_order" class="btn green-meadow" data-toggle="modal" href="#createOrderModal" title="Create PO Order">Create Order</button>&nbsp;&nbsp;
                            @endif
                        @endif
                    @endif
                    
                    @if($productArr[0]->po_status == '87001' && in_array($productArr[0]->approval_status, [57107,57119,57120]))
                    <a class="btn green-meadow" href="/grn/create/{{$productArr[0]->po_id}}" target="_blank" id="updatepo">Create GRN</a>&nbsp;&nbsp;
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                @include('PurchaseOrder::navigationTab')
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" id="po_id" name="po_id" value="{{$productArr[0]->po_id}}"/>
@if($productArr[0]->po_so_status == 0)
    <!-- Create Order Modal -->
    <div class="modal modal-scroll fade in" id="createOrderModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                    <h4 class="modal-title" id="basicvalCode">Create Order from PO</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                          <!-- DC -->
                          <div class="form-group">
                            <label class="control-label">Select a DC:<span class="required">*</span> </label>
                            @if(isset($allDcs) and !empty($allDcs))
                            <select class="form-control select2me" name="new_order_dc_id" id="new_order_dc_id">
                              <option value="">-Select-</option>
                              @foreach($allDcs as $dc)
                                  <option value="{{$dc->lp_wh_id}}">{{$dc->lp_wh_name}}</option>
                              @endforeach
                            </select>
                            @endif
                          </div>
                        </div>
                        <div class="col-md-4">
                          <!-- Hub -->
                          <div class="form-group">
                            <label class="control-label">Select a Hub:<span class="required">*</span> </label>
                            @if(isset($allHubs) and !empty($allHubs))
                            <select class="form-control select2me" name="new_order_hub_id" id="new_order_hub_id">
                              <option value="">-Select-</option>
                              @foreach($allHubs as $hub)
                                  <option value="{{$hub->lp_wh_id}}">{{$hub->lp_wh_name}}</option>
                              @endforeach
                            </select>
                            @endif
                          </div>
                        </div>
                        <div class="col-md-4">
                          <!-- State -->
                          <div class="form-group">
                            <label class="control-label">Select the State:<span class="required">*</span> </label>
                            @if(isset($allStates) and !empty($allStates))
                            <select class="form-control select2me" name="new_order_state_id" id="new_order_state_id">
                              @foreach($allStates as $state)
                                  <option value="{{$state->state_id}}">{{$state->state_name}}</option>
                              @endforeach
                            </select>
                            @endif
                          </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                      @if(isset($productArr) and !empty($productArr))
                        <div class="col-md-12">
                          <table class="table table-striped table-bordered table-advance table-hover table-scrolling">
                            <thead>
                              <tr>
                                <th></th>
                                <th>S.No</th>
                                <th>Product Name (SKU)</th>
                                <th>Qty</th>
                                <th>MRP</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $sno=1; ?>
                              @foreach($productArr as $product)
                                <tr class="odd gradeX">
                                  <td> <input type="checkbox" name="productArr[]" value="{{$product->product_id}}" checked="true"> </td>
                                  <td align="center">{{$sno++}}</td>
                                  <td> <strong>{{ $product->product_title }}<strong> ({{$product->sku}}) </td>
                                  <td>{{ $product->qty }} @if(isset($packTypes[$product->uom])) {{$packTypes[$product->uom]}} @endif {{'('.$product->qty*$product->no_of_eaches.' Eaches)'}} 
                                    <input type="hidden" name="qty_{{$product->product_id}}"  id="qty_{{$product->product_id}}" value="{{ $product->qty }}"></td>
                                  <td> {{ $product->mrp }} </td>
                                  <td> {{ $product->unit_price }} </td>
                                  <td> {{ $product->sub_total }} </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      @endif
                    </div>
                </div>
                <div class="modal-footer">
                  <div class="row">
                      <div class="col-md-12 text-center">
                          <button class="btn green-meadow" id="placeNewOrder">Place Order</button>
                          <button class="btn" id="cancelNewOrder" data-dismiss="modal">Cancel</button>
                      </div>
                  </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>


    @include('PurchaseOrder::Form.poErrorModelPopup')

@endif
@if(isset($featureAccess['closeFeature']) && $featureAccess['closeFeature'] && $productArr[0]->po_status!='87002')
<?php /*
<div class="modal modal-scroll fade in" id="closePOModel" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="basicvalCode">{{$labelName}}</h4>
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
@endif


<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop
@section('style')
<style type="text/css">

    .priceerrorname {
        color: black;
        font-weight: bold;
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
    .closedownload{ border:1px solid #ddd; width:25px; height:25px; padding:5px; margin:5px 0px;}
    .downloadclose{
        position: relative;
        left: 34px;
        top: 9px; font-size:12px !important;color:#F3565D;
        text-decoration: none;
        cursor: pointer;
    }
    .rightAlignment { text-align: right;}
    .centerAlignment { text-align: center;}
    #poInvoiceList > thead > tr > th.ui-iggrid-header:nth-child(5), #poInvoiceList > thead > tr > th.ui-iggrid-header:nth-child(6),
    #poPaymentList > thead > tr > th.ui-iggrid-header:nth-child(5){
        text-align: right !important;
    }
    #poPaymentList > thead > tr > th.ui-iggrid-header:nth-child(6),
    #poInvoiceList > thead > tr > th.ui-iggrid-header:nth-child(7){
        text-align: center !important;
    }
</style>
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/image-box/css/style.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/approvalscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/poscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/payments_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/image-box/js/jquery.imagebox.js') }}" type="text/javascript"></script>
<script>
getPoDetail('{{$productArr[0]->po_id}}');

$(document).ready(function () {
    // Here we set State Id to Telangana as Default
    $('#new_order_state_id option[value="4033"]').attr("selected",true);

    $('#closePObtn').click(function(){
        var po_id = $('#po_id').val();
        var po_status_val = $('#po_status_val').val();
        var close_reason = $('#close_reason').val();
        var type= 'cancel';
        if(po_status_val=='1'){
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
    $('#split_po').click(function(){
        var href = $(this).attr('data-url');
        if(confirm('Do you want to Split PO?')){
            $('.close').click();
            $('.loderholder').show();
            $.ajax({
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                url: href,
                type: 'POST',
                data: {},
                dataType:'JSON',
                success: function (data) {
                    if (data.status == 200) {
                        alert(data.message);
                        $('.loderholder').hide();
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
    $('#placeNewOrder').click(function(){
      const createNewOrder = confirm("Are you sure, to place a New Order?");
      if(createNewOrder){
        $('.loderholder').show();
        // Sanitizing the Product Ids from the Array
        let productArr = [];
        $("input[name^='productArr']").each(function (){
            if($(this).prop('checked')){
                var qty = $('#qty_'+$(this).val()).val();
                var arry = {product_id:$(this).val(),qty:qty}
                productArr.push(arry);
            }
        });
        // Dc Id from the Modal
        const dc_id = $("#new_order_dc_id").val();
        if(dc_id === undefined || dc_id == "" || dc_id == null){
          alert("Please select a DC");
          return false;
        }
        // Hub Id from the Modal
        const hub_id = $("#new_order_hub_id").val();
        if(hub_id === undefined || hub_id == "" || hub_id == null){
          alert("Please select a Hub");
          return false;
        }
        // State Id from the Modal bro
        const state_id = $("#new_order_state_id").val();
        if(state_id === undefined || state_id == "" || state_id == null){
          alert("Please select a State");
          return false;
        }
        $(this).attr('disabled',true);
        // An Ajax call to place the items to cart and intiate an Order
        $.ajax({
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/po/create_order_to_po/"+$('#po_id').val(),
            type: 'POST',
            data: {
              "product_ids": productArr,
              "dc_id": dc_id,
              "hub_id": hub_id,
              "state_id": state_id,
              "po_id":$("#po_id").val()
            },
            dataType: 'JSON',
            success: function (data) {
                console.log(data);
                if (data.status == "success") {
                    alert(data.message);
                    $('#createOrderModal').modal('toggle');
                    $('.loderholder').hide();
                    location.reload();
                } else if (data.message == 'pricing_mismatch_found'){
                    $('#createOrderModal').modal('toggle');
                    $("#prcingMismatchModal").modal('toggle');
                    $('.loderholder').hide();
                    $('#placeNewOrder').attr('disabled',false);
                    $('#priceMismatchData').html(data.data);
                    $('#reason_po_so').html(data.reason);
                    $('#po_so_adjust_message').html(data.adjust_message);
                } else {
                    $('#createOrderModal').modal('toggle');
                    alert(data.message);
                    $('.loderholder').hide();
                    $('#placeNewOrder').attr('disabled',false);
                }
            }
        });
      }
    });
</script>
@stop
