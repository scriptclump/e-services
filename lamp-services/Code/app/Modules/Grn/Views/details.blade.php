@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/grn/index">GRN</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>GRN Details</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> GRN# {{$grnProductArr[0]->inward_code}}</div>
                <div id="ajaxResponseDeliver" class="alert" style="display: none;"></div>
                <div class="actions pull-right margin-top-10">
                    @if(isset($featureAccess['printFeature']) && $featureAccess['printFeature'])
                    <a target="_blank" class="btn green-meadow" href="/grn/print/{{$grnProductArr[0]->inward_id}}"><i class="fa fa-print"></i></a>
                    @endif
                    @if(isset($featureAccess['downloadFeature']) && $featureAccess['downloadFeature'])
                    &nbsp;&nbsp;<a class="btn green-meadow" href="/grn/pdf/{{$grnProductArr[0]->inward_id}}"><i class="fa fa-download"></i></a>
                    @endif
                    @if(isset($invoiceExist) && $invoiceExist==0 && isset($featureAccess['grnEditFeature']) && $featureAccess['grnEditFeature'])
                    &nbsp;&nbsp;<a href="/grn/edit/{{$grnProductArr[0]->inward_id}}" class="btn green-meadow">Edit GRN</a>
                    @endif
                    @if(isset($grnProductArr[0]->po_no) && $grnProductArr[0]->po_no!='' && isset($featureAccess['poDetailFeature']) && $featureAccess['poDetailFeature'])
                    &nbsp;&nbsp;<a href="/po/details/{{$grnProductArr[0]->po_no}}" target="_blank" class="btn green-meadow">View PO</a>
                    @endif
                    @if( ($orderDelivered==true) && isset($featureAccess['createOrderFeature']) && $featureAccess['createOrderFeature'])
                    &nbsp;&nbsp;<button gds_order_id="{{$gds_order_id}}" po_id="{{$po_id}}" id="deliver_so_order" class="btn green-meadow">Full Deliver</button>
                    @endif
                    @if(isset($invoiceExist) && $invoiceExist==0 && isset($featureAccess['poInvoiceFeature']) && $featureAccess['poInvoiceFeature'])
                    &nbsp;&nbsp;<button href="/po/createPOInvoice/{{$grnProductArr[0]->inward_id}}" target="_blank" id="po_invoice" class="btn green-meadow">Create PO Invoice</button>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs nav-tabs-lg">
                        <?php $actionName = Request::segment(1) . '/' . Request::segment(2);?>
                        <li class="{{($actionName == 'grn/details' ? 'active' : '')}}"> <a href="#tab_1" data-toggle="tab"> Details </a> </li>
                        <li> <a href="#tab_2" data-toggle="tab"> Documents</a> </li>
                        <li> <a href="#tab_8" data-toggle="tab"  onclick="poInvoiceList({{$grnProductArr[0]->po_no}});"> Invoices
                                <span class="badge badge-success" id="totalInvoices">@if(isset($invoiceCount)){{$invoiceCount}} @endif</span>
                            </a> </li>
                        <li class=""><a href="#tab33" class="potabs" data-type="po" data-id="{{$grnProductArr[0]->po_no}}" data-toggle="tab" onclick="poPaymentList({{$leId}});" aria-expanded="true">Payments
                                <span class="badge badge-success" id="totalPayments">@if(isset($totalPayments)){{$totalPayments}} @endif</span></a>
                        </li>
                        <li> <a href="#tab_5" data-toggle="tab" onclick="purchaseReturnGrid('total','{{$grnProductArr[0]->inward_id}}');">Returns <span class="badge badge-success" id="totalInvoices">@if(isset($totalReturns)){{$totalReturns}} @endif</span> </a> </li>
                        @if($actionName == 'return/createreturn')
                        <li class="{{($actionName == 'return/createreturn' ? 'active' : '')}}"> <a href="#tab_6" data-toggle="tab">Create Return</a> </li>
                        @endif
                       <?php /* @if($actionName == 'return/returndetails')
                        <li class="{{($actionName == 'return/returndetails' ? 'active' : '')}}"> <a href="#tab_6" data-toggle="tab">Return Details</a> </li>
                        <li class=""> <a href="#tab_7" data-toggle="tab" onclick="getReturnHistory('{{$returnId}}');">Return Approval History</a> </li>
                        @endif */ ?>
                        <li> <a href="#tab_3" data-toggle="tab" onclick="getDispute('{{$grnProductArr[0]->inward_id}}');"> Comments </a> </li>
                        <li> <a href="#tab_4" data-toggle="tab">Approval History</a> </li>
                    </ul>                
                <div class="tab-content">
                    <div id="ajaxResponse1" class="alert" style="display: none;"></div>
                    <div class="tab-pane {{($actionName == 'grn/details' ? 'active' : '')}}" id="tab_1">
                        @include('Grn::Form.grnDetail')
                        <div class="row">
                            <div class="col-md-4">
                                @if($isApprovalFinalStep==1)
                                   @if($invoiceExist==1)
                                        @include('PurchaseOrder::Form.approvalForm')
                                   @else
                                   <span class="alert alert-warning">Please Create Invoice to Approve</span>
                                   @endif
                                @else
                                    @include('PurchaseOrder::Form.approvalForm')
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_2">
                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <table class="table table-striped table-bordered table-advance table-hover">
                                    <thead>
                                        <tr>
                                            <th>Document Type</th>
                                            <th>Ref No</th>
                                            <th>Created By</th>
                                            <th style="text-align:center;">Attachment</th>
                                            <th style="text-align:center;">Action</th>
                                        </tr>
                                    </thead>                    
                                    @if(isset($docsArr) && count($docsArr) > 0)
                                    @foreach($docsArr as $doc)                        
                                    <tr <?php if($doc->allow_duplicate) echo "style='color:red;'";?>>
                                        <td>{{$doc->doc_type}}</td>
                                        <td><input type="text" id="reference_no_<?php echo $doc->inward_doc_id?>" value="{{$doc->doc_ref_no}}" readonly="readonly"></td>
                                        <td>{{$doc->fullname}}</td>
                                        <?php 
                                            $url = '#';
                                            if (strpos($doc->doc_url,'http') !== false) {
                                                $url = $doc->doc_url;
                                            }else{
                                                $url = '/grn/download?file='.$doc->doc_url;
                                            }
                                        ?>
                                        <td align="center"><a href="{{$url}}"><i class="fa fa-download"></i></a></td>
                                        <td align="center">
                                            @if($featureAccess['grnreferencenoedit'])
                                            <span id="edit_<?php echo $doc->inward_doc_id?>" style="display:block"><a onclick="edit_referenceno({{$doc->inward_doc_id}})"><i class="fa fa-edit"></i></a></span>
                                            <span id="save_<?php echo $doc->inward_doc_id?>" style="display:none"><a onclick="save_referenceno({{$doc->inward_doc_id}})"><i class="fa fa-save"></i></a></span>
                                            @endif
                                            @if(Session('userId') == $doc->created_by)
                                            <a class="delete grn-del-doc" id="{{$doc->inward_doc_id}}" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else    
                                    <tr>
                                        <td colspan="5">No Record Found</td>
                                    </tr>
                                    @endif       
                                </table>
                            </div>
                        </div>      
                    </div>
                    <div class="tab-pane" id="tab_3">
                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <form id="frmSaveComment" action="/grn/createDisput" method="post">
                                    <h4>Comments</h4>
                                    <div id="ajaxResponse"></div>
                                    <textarea class="form-control" id="comment" name="comment"></textarea>
                                    <br>
                                    <input type="checkbox"  name="notifyByEmail"><span> Notify in email</span>
                                    <br>
                                    <input class="btn btn-success margtop" type="submit" name="saveComment" value="Submit">
                                    <input type="hidden" name="inwardId" id="inwardId" value="{{$grnProductArr[0]->inward_id}}">
                                </form>  
                            </div>
                            <div class="col-md-4 col-sm-4">
                                &nbsp;
                            </div>
                        </div>
                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <table id="dispute_list" class="table table-hover table-bordered table-striped">

                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_5">
                        <div class="text-right" style="float: right; font-size:11px;"><b>* All Amounts in <i class="fa fa-inr" aria-hidden="true"></i></b></div>
                        <div class="row">&nbsp;</div>
                        @if($totalRecvedQty>$totalReturnQty && isset($featureAccess['returnCreateFeature']) && $featureAccess['returnCreateFeature'])
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <a href="/return/createreturn/{{$grnProductArr[0]->inward_id}}" class="btn green-meadow" style="float:right">Create Return</a>
                            </div>
                        </div>
                        <div class="row">&nbsp;</div>
                        @endif
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <table id="prList" class="table table-hover table-bordered table-striped">

                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_7">
                        <div>&nbsp;</div>
                        <div id="returns_history"></div>
                    </div>
                    <div class="tab-pane" id="tab_8">
                        <div class="text-right" style="float: right; font-size:11px;"><b>* All Amounts in <i class="fa fa-inr" aria-hidden="true"></i></b></div>
                        <div>&nbsp;</div>                        
                        <table id="poInvoiceList"></table>
                    </div>
                    @include('PurchaseOrder::Form.payments')
                    <div class="tab-pane" id="tab_4">
                        <div>&nbsp;</div>
                        @include('PurchaseOrder::Form.approvalHistory')
                    </div>
                    @if($actionName == 'return/createreturn')                    
                    <div class="tab-pane {{($actionName == 'return/createreturn' ? 'active' : '')}}" id="tab_6">
                        <div id="ajaxResponse11" class="alert" style="display: none;"></div>
                        @if(isset($featureAccess['returnCreateFeature']) && $featureAccess['returnCreateFeature'])
                        @include('Grn::createReturn')
                        @else
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group alert alert-danger">You don't have access to create return.</div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($actionName == 'return/returndetails')
                    <div class="tab-pane {{($actionName == 'return/returndetails' ? 'active' : '')}}" id="tab_6">
                        <div id="returndetails"></div>
                    </div>
                    @endif
                </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('style')
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">

@media screen and (max-width: 480px) and (min-width: 320px)
.green-meadow.btn {
    margin-right: -7px !important;
}

.portlet > .portlet-title > .actions > .btn {
    padding: 4px 8px !important;
}

    .margtop{margin-top:22px;}
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        border-top: 0px !important;
    }
    .favfont i{font-size:18px !important; color:#5b9bd1 !important;}
    .tabbable-line > .nav-tabs > li > a > i {
        color: #5b9bd1 !important;
    }
    .well1 {
        border: 1px solid #eee !important;
            background:none !important;
            border-radius: 0px !important;
            height:387px;
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
    .margtop{margin-top:5px;}
    .rightAlignment{text-align: right;}
    .centerAlignment { text-align: center;}
	.ui-iggrid-table {
   border-collapse:collapse !important;
}
#sample_3.table>thead>tr>th, #sample_3.table>tbody>tr>td {
 text-align: right;
}
</style>
@stop
@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/approvalscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/poscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/payments_script.js') }}" type="text/javascript"></script>
<script>
function edit_referenceno(rid){
    $("#reference_no_"+rid).prop("readonly", false);
    $("#edit_"+rid).css('display','none');
    $("#save_"+rid).css('display','block');
  } 


  function save_referenceno(rid){
    $("#reference_no_"+rid).prop("readonly", true);
    $("#edit_"+rid).css('display','block');
    $("#save_"+rid).css('display','none');
    var reference_value=$("#reference_no_"+rid).val();

    $.ajax({
       headers:{'X-CSRF-Token': $('input[name="_token"]').val()}, 
       url:"/grn/savereferenceno",
       type:"POST",
       data:{
          rid:rid,
          reference_value:reference_value
       },
       success: function(response) {
                   alert(response);
                }

    })
  }
var skus = '';
$( document ).ready(function() {
    $("#frmUpload").validate({
        rules: {
            documentType: {
                      required: true
                  },
                  ref_no: {
                      required: false
                  },
                  upload_file: {
                                    required: true,
                                    //extension: "pdf|doc|docx|jpg|jpeg|png|gif"
                                  }
              },  
      submitHandler: function(form) {
        $.ajax({
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/grn/uploadDoc",
            type: "POST",
            data: new FormData(form),
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                $('#ajaxResponseDoc').html(response.message);
                window.location.reload();
            },
            error:function(response){
                $('#ajaxResponseDoc').html('Unable to saved comment');
            }
    });
      }
     });
    /* if('{{$actionName}}' == 'return/returndetails' && '{{$returnId}}'!=0){
         getReturnDetails('{{$returnId}}');
     } */
});

function filterComment(txnId) {
    var filterURL = "/grn/getDisput/"+txnId;
    
    $("#dispute_list").igGrid({
                        dataSource: filterURL,
                        autoGenerateColumns: false
                    });
}

$("#deliver_so_order").click(function(){
    $('.loderholder').show();
    var gds_order_id =  $(this).attr('gds_order_id');
    var po_id = $(this).attr('po_id');
    $.ajax({
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/grn/deliverorderongrn/"+gds_order_id+'/'+po_id,
            type: "GET",
            data: "",
            success: function(response) {
                var response = JSON.parse(response);
                $('.loderholder').hide();
                if(response.status == 200){
                    $("#deliver_so_order").hide();    
                }
                alert(response.message);
            },
            error:function(response){

                $('.loderholder').hide();
                alert("Unable to deliver Order!");

            }
    });
})

function saveComment() {
    var inwardId = $('#inwardId').val();
    var comment = $('#comment').val();
    var formData = $('#frmSaveComment').serialize();
     $.ajax({
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/grn/createDisput",
            type: "POST",
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#ajaxResponse').addClass('text-success').html(response.message);
                window.setTimeout(function(){location.reload()},1000)
                //filterComment('{{$grnProductArr[0]->inward_id}}');
                //window.location.reload();
            },
            error:function(response){
                $('#ajaxResponse').html('Unable to saved comment');
            }
    });    
}

function deleteDoc(id) {
    $.ajax({
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                url: "/grn/delete",
                type: "POST",
                data: {id:id},
                dataType: 'json',
                success: function(response) {
                    $('#ajaxResponse').html(response.message);
                    window.location.reload();
                },
                error:function(response){
                    $('#ajaxResponse').html('Unable to delete');
                }
        });
}
/*
function getReturnDetails(id) {
    $('.loderholder').show();
    $.ajax({
        headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
        url: "/pr/details/"+id,
        type: "POST",
        success: function(response) {
            $('#returndetails').html(response);
            $('.loderholder').hide();
        },
        error:function(response){
            $('#ajaxResponse').html('Unable to get data');
        }
    });
}
function getReturnHistory(id) {
    $('.loderholder').show();
    $.ajax({
        headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
        url: "/return/getReturnHistory/"+id,
        type: "POST",
        success: function(response) {
            $('#returns_history').html(response);
            $('.loderholder').hide();
        },
        error:function(response){
            $('#ajaxResponse').html('Unable to get data');
        }
    });
}
*/

$( document ).ready(function() {

    $( ".grnItem").click(function() {
        var itemId = $( this ).attr("id");

      $( "#packinfo-"+itemId ).toggle( "slow", function() {
      });

      if($(this).find('i').hasClass('fa-plus')) {
        $(this).find('i').removeClass('fa-plus').addClass('fa-minus');
      }
      else if($(this).find('i').hasClass('fa-minus')) {
        $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
      }

    });  

    $(".grn-del-doc").click(function(){
        var docId = $( this ).attr("id");
        if(confirm('Do you want to delete this document?')) {
            deleteDoc(docId);
        }
    });
    $("#po_invoice").click(function(){
        if(confirm('Do you want to create invoice?')) {
            $('.loderholder').show();
            $.ajax({
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/po/createPOInvoice/'+{{$grnProductArr[0]->inward_id}},
                type: "GET",
                data: {},
                dataType: 'json',
                success: function(response) {
                    if(response.status==200){
                        $('#ajaxResponse1').removeClass('alert-danger').addClass('alert-success').html(response.message).show();
                        window.setTimeout(function(){window.location.reload();},1000);
                    }else{
                        $('#ajaxResponse1').removeClass('alert-success').addClass('alert-danger').html(response.message).show();
                    }
                    $('.loderholder').hide();
                },
                error:function(response){
                    $('#ajaxResponse1').removeClass('alert-success').addClass('alert-danger').html('Unable to create invoice').show();
                    $('.loderholder').hide();
                }
            });
        }
    });

    $("#frmSaveComment").validate({
        rules: {
                comment: "required"
            },
            submitHandler: function(form) {
                //form.submit();
                saveComment();
            }
    });
    $("#saveReturn").validate({
            rules: {
                /*documentType: {
                    required: false
                },
                ref_no: {
                    required: false
                },*/
            },
            submitHandler: function (form) {
                var formData = $('#saveReturn').serialize();
                $('.loderholder').show();
                if(checkProductQty()){
                $.ajax({
                    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                    url: "/return/saveReturn",
                    type: "POST",
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if(data.status==200){
                            $('.loderholder').hide();
                            $('#ajaxResponse11').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                            $('html, body').animate({scrollTop: '0px'}, 500);
                            window.setTimeout(function(){window.location.href = '/pr/details/'+ data.pr_id},1000);
                        }else{
                            $('#ajaxResponse11').removeClass('alert-success').addClass('alert-danger').html(data.message).show();
                            $('.loderholder').hide();
                            $('html, body').animate({scrollTop: '0px'}, 500);
                        }
                    },
                    error: function (response) {
                        $('#ajaxResponse11').html('Unable to create return');
                    }
                });
                }else{
                    $('#ajaxResponse11').removeClass('alert-success').addClass('alert-danger').html("{{Lang::get('po.alertPOQty')}}"+" for "+skus).show();
                    $('.loderholder').hide();
                    $('html, body').animate({scrollTop: '0px'}, 500);
                }
            }
        });
        $('#checkall').click(function(){
           $('.check').attr('checked',false);
           if($('#checkall').is(':checked')){
               $(".check").each(function () {
                    $(this).prop("checked", true);
               });
           }
        });
    $('.return_qty').change(function(){
        var product_id = $(this).closest('tr').find('.product_id').val();
        var qty = 0;
        soh_qty = $('#soh_qty'+product_id).val();
        dit_qty = $('#dit_qty'+product_id).val();
        dnd_qty = $('#dnd_qty'+product_id).val();
        soh_qty = (soh_qty!='' && soh_qty>=0)?soh_qty:0;
        dit_qty = (dit_qty!='' && dit_qty>=0)?dit_qty:0;
        dnd_qty = (dnd_qty!='' && dnd_qty>=0)?dnd_qty:0;
        qty = parseInt(soh_qty)+parseInt(dit_qty)+parseInt(dnd_qty);
       if(qty>0){               
           $('#check'+product_id).prop('checked',true);
       }else{
           $('#check'+product_id).prop('checked',false);
       }
});
});
function checkProductQty() {
        skus='';
        var checked = true;
        $(".product_id").each(function () {
            var product_id = $(this).val();
            var soh_qty = $('#soh_qty'+product_id).val();
            var dit_qty = $('#dit_qty'+product_id).val();
            var dnd_qty = $('#dnd_qty'+product_id).val();
            var productQty = parseInt(soh_qty)+parseInt(dit_qty)+parseInt(dnd_qty);
            if ($('#check'+product_id).is(':checked') && (productQty == '' || productQty <= 0)) {
                var sku=$('#product_sku'+product_id).val();
                checked = false;
                skus = skus+sku+',';
                return;
            }
        });
        skus='<strong>'+skus.replace(/,\s*$/, "")+'</strong>';
        return checked;
    }
</script>
<style type="text/css">.loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
    .loderholder img{ position: absolute; top:50%;left:50%;    }
    .error{color: red;}
</style>

<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop