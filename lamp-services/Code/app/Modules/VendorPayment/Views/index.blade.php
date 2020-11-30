@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')

<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/po/index">Purchase Order</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Vendor Payments</li>
        </ul>
    </div>
</div>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Vendor Payments </div>
                <div class="actions">
                     @if( ($filter_status == $approvalStatuslist['completed']))
                     <a type="button" id="exportCSVComplete" class="btn green-meadow">Export</a>
                     <a type="button" id="" href="#downloadReport" data-toggle="modal" class="btn green-meadow">Download Report</a> 
                     
                </div>
            </div>
                <div class="portlet-body">
                    <form id="filter_val">
                <div class="row" style="position: relative;">
                    <div class="col-md-2">
                        <div class="form-group">
                        <input type="text" class="form-control " name="from_date" id="from_date"  placeholder="From Date" autocomplete="off" required>
                        </div>

                    </div>
                        <div class="col-md-2">
                        <div class="form-group">
                           <input type="text" class="form-control " name="to_date" id="to_date"  placeholder="To Date" autocomplete="off" required>
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control select2me" name="sup_name" id="sup_name" required autocomplete="off">
                                    <option value=''>Select Supplier Name</option>
                                    @foreach($suppliers as $supplier)
                                    @if($supplier->business_legal_name!='' || $supplier->business_legal_name!=null)
                                    <option value="{{ $supplier->legal_entity_id}}" >{{$supplier->business_legal_name}}</option>
                                    @endif
                                    @endforeach
                                
                                    </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                    <div class="form-group genra">
                        <button type="button" id="filter_button" class="btn green-meadow">Go</button>
                    </div>
                  </div>
                </div></form>

                @endif
                <div class="actions">
                	@if($filter_status=='')
                    <a class="btn green-meadow" href="#po_hold" data-toggle="modal">Move to Hold/Not to Pay</a>
                    @elseif(in_array($filter_status,[57222,57223]))
                    <a class="btn green-meadow" href="#po_hold" data-toggle="modal">Move to Pending</a>
                    @endif                    
                    @if( $filter_status == $approvalStatuslist['initiated'] ) 
                        @if( $acl['reject_payment_request_access'] ) 
                        <button type="button" value="{{ $approvalStatuslist['rejected'] }},0" class="btn green-meadow processRequest">Reject Payment Request</button>
                        @endif
                        @if( $acl['approve_payment_request_access'] ) 
                        <button type="button" value="{{ $approvalStatuslist['finance_approved'] }},0" class="btn green-meadow processRequest">Approve Payment Request</button>
                        @endif

                    @elseif( ($filter_status == $approvalStatuslist['finance_approved'] ||  $filter_status == $approvalStatuslist['failed_at_bank']) && $acl['export_excel_bank_payment_access'] )
                        <a type="button" id="exportCSV" class="btn green-meadow">Export</a>
                    @elseif( $filter_status == $approvalStatuslist['processing_with_bank']  && $acl['update_bank_payment_status_access'] )
                    
                        <button type="button" class="btn green-meadow" id="completePayment" style="
    padding-top: 10px;">Update Payment Status</button>
                        <div class="col-md-4" style="padding-left:15px; padding-top:-3px;">       
                        <a type="button" id="exportCsvUpdate" class="btn green-meadow">Export</a>
                    </div>
                    @elseif( $filter_status == '' && $acl['raise_payment_request_access']  )
                      <button type="button" class="btn green-meadow" id="raiseRequest">Raise Payment Request</button>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="row sortingborder">
                    <div class="col-md-12">
                        <div class="caption">
                            <span class="caption-subject bold font-blue"> Filter By :</span>
                            <span class="caption-helper sorting">
                                @if( $acl['pending'] ) 
                                <a href="{{$app['url']->to('/')}}/vendor/payments/" class="{{($filter_status == '') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Pending Payments">Pending ({{$pendingPaymentCount}})</a>&nbsp;&nbsp;
                                @endif
                                @if( $acl['initiated'] ) 
                                <a href="{{$app['url']->to('/')}}/vendor/payments/{{$approvalStatuslist['initiated']}}" class="{{($filter_status == $approvalStatuslist['initiated']) ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="PO Created">Initiation ({{$total_status[ $approvalStatuslist['initiated'] ]}})</a>&nbsp;&nbsp;
                                @endif

                                @if( $acl['approved'] )
                                <a href="{{$app['url']->to('/')}}/vendor/payments/{{$approvalStatuslist['finance_approved']}}" class="{{($filter_status == $approvalStatuslist['finance_approved']) ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Payment Approved">Approved ({{$total_status[ $approvalStatuslist['finance_approved'] ]}})</a>&nbsp;&nbsp;
                                @endif
                                @if( $acl['rejected'] )
                                <a href="{{$app['url']->to('/')}}/vendor/payments/{{$approvalStatuslist['rejected']}}" class="{{($filter_status == $approvalStatuslist['rejected']) ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Payment Rejected">Rejected ({{$total_status[ $approvalStatuslist['rejected'] ]}})</a>&nbsp;&nbsp;
                                @endif                               
                                <a href="{{$app['url']->to('/')}}/vendor/payments/{{$approvalStatuslist['failed_at_bank']}}" class="{{($filter_status == $approvalStatuslist['failed_at_bank']) ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Payment Rejected">Failed at bank ({{$total_status[ $approvalStatuslist['failed_at_bank'] ]}})</a>&nbsp;&nbsp;                             
                                @if( $acl['processing_with_bank'] )
                                <a href="{{$app['url']->to('/')}}/vendor/payments/{{$approvalStatuslist['processing_with_bank']}}" class="{{($filter_status == $approvalStatuslist['processing_with_bank']) ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Processing with the bank">Under process with bank ({{$total_status[ $approvalStatuslist['processing_with_bank'] ]}})</a>&nbsp;&nbsp; 
                                @endif
                                @if( $acl['completed'] )
                                <a href="{{$app['url']->to('/')}}/vendor/payments/{{$approvalStatuslist['completed']}}" class="{{($filter_status == $approvalStatuslist['completed']) ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Payment Completed">Completed ({{$completed}})</a>&nbsp;&nbsp; @endif             
                                <a href="{{$app['url']->to('/')}}/vendor/payments/{{$approvalStatuslist['hold']}}" class="{{($filter_status == $approvalStatuslist['hold']) ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Payment Hold PO's">Hold ({{$poCounts['hold']}}) </a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/vendor/payments/{{$approvalStatuslist['not_to_pay']}}" class="{{($filter_status == $approvalStatuslist['not_to_pay']) ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Pamyment not Required PO's">Not to Pay ({{$poCounts['not_to_pay']}}) </a>&nbsp;&nbsp;

                                <a href="{{$app['url']->to('/')}}/vendor/payments/allpo" class="{{($filter_status == 'allpo') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Total PO's from Vendors not include DC/FC po">Total ({{$poCounts['all']}}) </a>&nbsp;&nbsp;
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        &nbsp;
                        <span style="float:right;font-size: 11px;font-weight: bold;">* All Amounts in (₹)</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                            <table id="poList"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modalbox for showing the history -->
<div class="modal fade" id="view-history" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"> VIEW HISTORY</h4>
            </div>
            <div class="modal-body">    
            <div id="historyContainer">
                            </div>                      
                
        </div>
    </div>
</div>
</div>
<!-- Modalbox for showing the history -->
<div class="modal fade" id="po_hold" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="width:40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Update PO payment status</h4>
            </div>
            <div class="modal-body">                          
                <form id="stockTransferForm" class="" method="get">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="display:none;" id="appr_error-msg" class="alert alert-danger"></div>
                            <div class="form-group">
                                <label class="control-label"><strong>Status</strong></label>
                                <select name="approval_status" id="approval_status" class="select2me form-control">
                                    @if($filter_status=="")
                                        <option value="57222,0">Hold</option>
                                        <option value="57223,0">Not to Pay</option>
                                    @else
                                        <option value="57224,0">Pending</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label"><strong>Comment</strong></label>
                                <textarea class="form-control" name="approval_comment" id="approval_comment"></textarea>    
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button name="approval_submit" id="approval_submit" class="btn green-meadow">Submit</button>       
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-scroll fade in" id="downloadReport" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Complete Payment Report</h4>
            </div>
            <div class="modal-body">
             <form id="downloadCompleteReport" action="/vendor/downloadCompleteReport" class="text-center" method="post">
                    <div class="row">
                        <div class="col-md-12" align="center">

                    <div class="col-md-8">
                        <div class="form-group">
                            <select class="form-control select2me" name="sup_name" required id="sup_name"  autocomplete="off">
                                    <option value=''>Select Supplier Name</option>
                                    <option value="all">All</option>
                                    @foreach($suppliers as $supplier)
                                    @if($supplier->business_legal_name!='' || $supplier->business_legal_name!=null)
                                    <option value="{{ $supplier->legal_entity_id}}" >{{$supplier->business_legal_name}}</option>
                                    @endif
                                    @endforeach
                                
                            </select> 
                              </div>
                    </div>
                            <div style="display:none;" id="error-msg1" class="alert alert-danger"></div>                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="gstfdate" name="fdate" class="form-control" required placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="gsttdate" name="tdate" class="form-control" required placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>   
                        </div>
                    </div>
                 <!--    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: default current month data will download
                        </div>
                    </div> -->
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="uploadfile" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


 {{HTML::style('css/switch-custom.css')}}
 @include('includes.validators')
     @stop
@section('style')
      <link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
      <link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />

<style type="text/css">
    /*.portlet > .portlet-title { margin-bottom:0px !important;}*/
    .spaceLeft{
        padding-left:10px !important;
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

    .SumoSelect > .optWrapper > .options {
    text-align: left;
    }
    .favfont i{font-size:18px !important;}
    .actionss{padding-left: 22px !important;}

    .sortingborder{border-bottom:1px solid #eee;border-top:1px solid #eee; padding:10px 0px;}

    .sorting a{ list-style-type:none !important;text-decoration:none !important;font-size: 12px;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#676767 !important;}

    .fa-eye{color:#3598dc !important;}
    .fa-print{color:#3598dc !important;}
    .fa-download{color:#3598dc !important;}
    #poList_poId{padding-left:8px !important;}
    #poList_Supplier{padding-left:6px !important;}
    #poList_Status{padding-left:-3px !important;}
    #poList_fixedBodyContainer{overflow-x: auto!important;}
    .nowrap{
        white-space: nowrap !important;
    }
    .ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{
        border-spacing: 0px !important;
    }
    #poList_fixedContainerScroller{
         height: 0px !important;
    }
    .centerAlignment { text-align: center;}
    .rightAlignment { text-align: right;}
   /* th.ui-iggrid-header:nth-child(6), th.ui-iggrid-header:nth-child(7), th.ui-iggrid-header:nth-child(8){
        text-align: right !important;
    } */
.SumoSelect > .optWrapper > .options {
    text-align: left;
}
#poList_pager_label{
    margin-left: -20%;
}   
.submitRequest{
    margin-top: 10px;
    margin-right: 10px;
}

.inputBorder{
    border: 1px solid #000;
}

</style>
@stop

@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/VendorPayment/data-grids.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
        $(document).ready(function () {
            /**
             * Submit the PO payment request
             * @param  array   var checkboxes Purchase order ID's
             * @return HTML     Message for successful or fail request
             */
            $("#approval_submit").click(function(e){
                e.preventDefault();
                token  = $("#csrf-token").val();
                //var checkboxes = $('input[type=checkbox]:checked').attr('class');
                var requestIds = [];
                $.each($("input:checkbox[class=check_box]:checked"), function(){
                     requestIds.push($(this).val());
                });
                var approvalStatus = $( "#approval_status").val();
                var approvalComment = $( "#approval_comment").val();

                var postData = {
                    "requestIds": requestIds,
                    "approval_status": approvalStatus,
                    "approval_comment": approvalComment
                };
                if (requestIds.length === 0) {
                    alert('Please select at least one purchase order');
                    return false;
                }else{
                    $(this).attr("disabled","disabled");
                   // console.log(postData);
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: "/vendor/popaymentstatusupdate",
                        type: 'POST',
                        data: postData,
                     //   async: false,
                        beforeSend: function (xhr) {
                            $('.spinnerQueue').show();
                        },
                        success: function (data) {
                           alert('Payment status updated successfully');
                           window.location = '/vendor/payments';
                           //$(this).removeAttr("disabled");
                        }
                    });
                }    
            });
        });
    </script>
@if( $filter_status == $approvalStatuslist['initiated'] ) 
    <script type="text/javascript">
        $(document).ready(function () {
            initiationGrid('{{$filter_status}}');
            /**
             * Submit the PO payment request
             * @param  array   var checkboxes Purchase order ID's
             * @return HTML     Message for successful or fail request
             */
            $(".processRequest").click(function(){
                token  = $("#csrf-token").val(); 
                var checkboxes = document.getElementsByClassName('check_box');
                var requestIds = [];
                var approvedAmt = {};
                var approverComment = {};                
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].checked) {
                        poId = checkboxes[i].value;
                        amt  = document.getElementById('pendingAmt'+poId).value;
                        comments = document.getElementById('comments'+poId).value;
                        requestIds.push(poId);
                        approvedAmt[poId]= amt;
                        approverComment[poId]= comments;
                    }
                }
                var postData = {
                    "requestIds": requestIds,
                    "approvedAmt": approvedAmt,
                    "stageStatus": $(this).val(),
                    "comments": approverComment
                };
                if (requestIds.length === 0) {
                    alert('Please select at least one purchase order');
                }else{
                   // console.log(postData);
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: "/vendor/approvalSubmit",
                        type: 'POST',
                        data: postData,
                     //   async: false,
                        beforeSend: function (xhr) {
                            $('.spinnerQueue').show();
                        },
                        success: function (data) {
                           alert('Payment status updated successfully');
                           window.location = '/vendor/payments/{{$approvalStatuslist["initiated"]}}';
                        }
                    });
                }    
            });          
        });
    </script>   
@elseif(  $filter_status == $approvalStatuslist['processing_with_bank'] )
    <script type="text/javascript">
        $(document).ready(function () {
            bankProcessGrid('{{$filter_status}}');
            /**
             * Update the status of the payment
             * @param  array   var checkboxes Purchase order ID's
             * @return HTML     Message for successful or fail request
             */
            $("#completePayment").click(function(){
                token                = $("#csrf-token").val(); 
                var checkboxes       = document.getElementsByClassName('check_box');
                var requestIds       = [];
                var bank_accounts    = {};
                var bank_statuses    = {};
                var utrs             = {};
                var payment_dates    = {};
                var payment_types    = {};
                var approved_amounts = {};
                var comments         = {};
                var poIds            = {}; 
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].checked) {
                        requestId        = checkboxes[i].value;
                        bank_account_ele = document.getElementById('bank_account'+requestId);
                        bank_status_ele  = document.getElementById('bank_status'+requestId);
                        payment_type_ele = document.getElementById('payment_type'+requestId);
                        bank_account     = bank_account_ele.options[bank_account_ele.selectedIndex].value;
                        bank_status      = bank_status_ele.options[bank_status_ele.selectedIndex].value;
                        payment_type     = payment_type_ele.options[payment_type_ele.selectedIndex].value;
                        
                        utr              = document.getElementById('utr'+requestId).value;
                        payment_date     = document.getElementById('payment_date'+requestId).value;
                        comment          = document.getElementById('comment'+requestId).value;
                        po_id            = document.getElementById('po_id'+requestId).value;
                        approved_amount  = document.getElementById('approved_amount'+requestId).value;
                        
                        requestIds.push(requestId);
                        bank_accounts[requestId]    = bank_account;
                        bank_statuses[requestId]    = bank_status;
                        payment_dates[requestId]    = payment_date;
                        utrs[requestId]             = utr;
                        poIds[requestId]            = po_id;
                        payment_types[requestId]    = payment_type;
                        approved_amounts[requestId] = approved_amount;
                        comments[requestId]         = comment;
                    }
                }
                var postData = {
                    "requestIds": requestIds,
                    "bank_accounts": bank_accounts,                    
                    "bank_statuses": bank_statuses,
                    "payment_dates": payment_dates,
                    "utrs": utrs,
                    "comments": comments,
                    "poIds": poIds,
                    "payment_types": payment_types,
                    "approved_amounts": approved_amounts                    
                };
                if (requestIds.length === 0) {
                    alert('Please select at least one purchase order');
                }else{
                    console.log(postData);
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: "/vendor/complete-payment",
                        type: 'POST',
                        data: postData,
                        //async: false,
                        beforeSend: function (xhr) {
                            $('.spinnerQueue').show();
                        },
                        success: function (data) {                           
                         $('.spinnerQueue').hide();
                         console.log(data);
                         window.location = '/vendor/payments/{{$approvalStatuslist["processing_with_bank"]}}';      
                        }
                    });
                }    
            });  
            $("#exportCsvUpdate").click(function(){
                token  = $("#csrf-token").val(); 
                var checkboxes = document.getElementsByClassName('check_box');
                var prIds = [];
                var requestAmt = {};
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].checked) {
                        prId     = checkboxes[i].value;
                        prIds.push(prId);
                    }
                }
                var postData = {
                    "prIds": prIds
                };
                if (prIds.length === 0) {
                    alert('Please select at least one purchase order');
                }else{
                   // console.log(postData);
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: "/vendor/payment-request-export-upwb",
                        type: 'POST',
                        data: postData,
                      //  async: false,
                        beforeSend: function (xhr) {
                            $('.spinnerQueue').show();
                        },
                        success: function (data) {
                            window.open('/vendor/payment-request-export-upwb/?download=excel','_blank');
                            window.location = '/vendor/payments/{{$approvalStatuslist["processing_with_bank"]}}'; 
                        }
                    });
                }    
            });
        });
    </script>
@elseif(  $filter_status == $approvalStatuslist['finance_approved'] )
    <script type="text/javascript">
        $(document).ready(function () {
            approvedGrid('{{$filter_status}}');
            /**
             * Export the selected items to process with bank
             * @param  array   var checkboxes Purchase order ID's
             * @return HTML     Message for successful or fail request
             */
            $("#exportCSV").click(function(){
                token  = $("#csrf-token").val(); 
                var checkboxes = document.getElementsByClassName('check_box');
                var prIds = [];
                var requestAmt = {};
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].checked) {
                        prId     = checkboxes[i].value;
                        prIds.push(prId);
                    }
                }
                var postData = {
                    "prIds": prIds
                };
                if (prIds.length === 0) {
                    alert('Please select at least one purchase order');
                }else{
                   // console.log(postData);
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: "/vendor/payment-request-export",
                        type: 'POST',
                        data: postData,
                      //  async: false,
                        beforeSend: function (xhr) {
                            $('.spinnerQueue').show();
                        },
                        success: function (data) {
                            window.open('/vendor/payment-request-export/?download=excel','_blank');
                            window.location = '/vendor/payments/{{$approvalStatuslist["finance_approved"]}}'; 
                        }
                    });
                }    
            });
        });
    </script>
@elseif(  $filter_status == $approvalStatuslist['rejected'] )
    <script type="text/javascript">
        $(document).ready(function () {
            rejectedGrid('{{$filter_status}}');
        });
    </script>
@elseif(  $filter_status == $approvalStatuslist['failed_at_bank'] )
    <script type="text/javascript">
        $(document).ready(function () {
            failedAtBankGrid('{{$filter_status}}');

            $("#exportCSV").click(function(){
                token  = $("#csrf-token").val(); 
                var checkboxes = document.getElementsByClassName('check_box');
                var prIds = [];
                var requestAmt = {};
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].checked) {
                        prId     = checkboxes[i].value;
                        prIds.push(prId);
                    }
                }
                var postData = {
                    "prIds": prIds
                };
                if (prIds.length === 0) {
                    alert('Please select at least one purchase order');
                }else{
                   // console.log(postData);
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: "/vendor/payment-request-export",
                        type: 'POST',
                        data: postData,
                      //  async: false,
                        beforeSend: function (xhr) {
                            $('.spinnerQueue').show();
                        },
                        success: function (data) {
                            $('.spinnerQueue').hide();
                            window.open('/vendor/payment-request-export/?download=excel','_blank');
                            window.location = '/vendor/payments/{{$approvalStatuslist["failed_at_bank"]}}'; 
                        }
                    });
                }    
            });
        });
    </script>
@elseif(  $filter_status == $approvalStatuslist['completed'] )
    <script type="text/javascript">
        $(document).ready(function () {
            @if( isset($filter_dates['from_date']) && isset($filter_dates['from_date'])  && isset($filter_dates['sup_name']) )
                bankPaymentCompleteGrid('{{$filter_status}}', '{{$filter_dates["from_date"]}}', '{{$filter_dates["to_date"]}}', '{{$filter_dates["sup_name"]}}'  );
            @elseif( isset($filter_dates['from_date']) && isset($filter_dates['from_date']) )
                bankPaymentCompleteGrid('{{$filter_status}}', '{{$filter_dates["from_date"]}}', '{{$filter_dates["to_date"]}}' );
            @else
                bankPaymentCompleteGrid('{{$filter_status}}');
            @endif

            
            $("#exportCSVComplete").click(function(){
                token  = $("#csrf-token").val(); 
                var checkboxes = document.getElementsByClassName('check_box');
                var prIds = [];
                var requestAmt = {};
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].checked) {
                        prId     = checkboxes[i].value;
                        prIds.push(prId);
                    }
                }
                var postData = {
                    "prIds": prIds
                };
                if (prIds.length === 0) {
                    alert('Please select at least one purchase order');
                }else{
                   // console.log(postData);
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: "/vendor/payment-request-complete-export",
                        type: 'POST',
                        data: postData,
                      //  async: false,
                        beforeSend: function (xhr) {
                            $('.spinnerQueue').show();
                        },
                        success: function (data) {
                            $('.spinnerQueue').hide();
                            window.open('/vendor/payment-request-complete-export/?download=excel','_blank');
                            window.location = '/vendor/payments/{{$approvalStatuslist["completed"]}}'; 
                        }
                    });
                }    
            });
        });
    </script>
@elseif(  $filter_status == '')
<script type="text/javascript">
    $(document).ready(function () {
        pendingPOGrid('{{$filter_status}}');
        /**
         * Approve the PO payment request
         * @param  array   var checkboxes Purchase order ID's
         * @return HTML     Message for successful or fail request
         */
        $("#raiseRequest").click(function(){
            token  = $("#csrf-token").val(); 
            var checkboxes = document.getElementsByClassName('check_box');
            var poIds = [];
            var requestAmt = {};
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    poId     = checkboxes[i].value;
                    amt      = document.getElementById('pendingAmt'+poId).value;
                    if(amt > 1){
                        poIds.push(poId);
                        requestAmt[poId]= amt;
                    }                        
                }
            }
            if(poIds.length > 0){
                var postData = {
                    "poIds": poIds,
                    "requestAmt": requestAmt
                };
                if (poIds.length === 0) {
                    alert('Please select at least one purchase order');
                }else{
                   // console.log(postData);
                   $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: "/vendor/raise-payment-request",
                        type: 'POST',
                        data: postData,
                     //   async: false, 
                        beforeSend: function (xhr) {
                            $('.spinnerQueue').show();
                        },                   
                        success: function (data) {
                           console.log('data');
                           console.log(data);
                           setTimeout(()=>{
                                $('.spinnerQueue').hide();
                           },2000);
                           alert(data.message);
                           window.location = '/vendor/payments';      
                        }
                    });
                   // setTimeout(()=>{
                   // $('.spinnerQueue').hide();

                   // },2000);
                }
            } else{
                alert("Please enter the correct request amount");
            } 
            
        });
    });
</script>
@else
    <script type="text/javascript">
        $(document).ready(function () {
            allPOGrid('{{$filter_status}}');           
        });
    </script>
@endif
<script type="text/javascript">

function viewhistory(id,module){
    $('#view-history').modal('toggle');
    $("#historyContainer").empty();
    token  = $("#csrf-token").val();
    module = (module=='PO')?'Purchase Order':'Vendor Payment Request'; 
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/po/getApprovalHistory/'+module+'/'+id,
        success: function( data ) { 
            console.log(data);
            $('#historyContainer').append(data);     
        }        
    });
}  


   $(document).ready(function () {

        window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
  
    });   
function filterPO(status) {
    var formData = $('#frm_po').serialize();
    $("#poList").igGrid({
        dataSource: "/po/ajax/" + status + "?" + formData,
        autoGenerateColumns: false
    });
}
function getNextDay(select_date) {
    select_date.setDate(select_date.getDate() + 1);
    var setdate = new Date(select_date);
    var nextdayDate = zeroPad((setdate.getMonth() + 1), 2) + '/' + zeroPad(setdate.getDate(), 2) + '/' + setdate.getFullYear();
    return nextdayDate;
}
function zeroPad(num, count) {
    var numZeropad = num + '';
    while (numZeropad.length < count) {
        numZeropad = "0" + numZeropad;
    }
    return numZeropad;
}

$(document).ready(function () {
    
    $('#fdate').datepicker({
        maxDate: 0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
            $('#tdate').datepicker('option', 'minDate', seldayDate);
        }
    });

    $('#tdate').datepicker({
        maxDate: 0,
    });
    $('#pofdate,#hsnfdate').datepicker({
        maxDate: 0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
            $('#potdate').datepicker('option', 'minDate', seldayDate);
        }
    });

    $('#potdate,#hsntdate').datepicker({
        maxDate: 0,
    });
   $('#gstfdate').datepicker({
    maxDate: 0,
    onSelect: function () {
        var select_date = $(this).datepicker('getDate');
        var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
        $('#gsttdate').datepicker('option', 'minDate', seldayDate);
    }
    });

    $('#gsttdate').datepicker({
        maxDate: 0,
    });

    $("#toggleFilter").click(function () {
        $("#filters").toggle("slow", function () {
        });
    });
    $.validator.addMethod("DateFormat", function (value, element) {
        if (value != '') {
            return value.match(/^(0[1-9]|1[012])[- //.](0[1-9]|[12][0-9]|3[01])[- //.](19|20)\d\d$/);
        } else {
            return true;
        }
    },
            "Please enter a date in the format mm/dd/yyyy"
            );
    $.validator.addMethod("maxDate", function (value, element) {
        var now = new Date();
        var tomorrow = new Date(now.getTime() + (24 * 60 * 60 * 1000));
        var myDate = new Date(value);
        return this.optional(element) || myDate <= tomorrow;
    },
            "should not be future date"
            );
    $.validator.addMethod("minDate", function (value, element) {
        var fdate = new Date($('#fdate').val());
        var myDate = new Date(value);
        return this.optional(element) || myDate >= fdate;
    },
            "should not be less than from date"
            );
    $('#exportPOForm').validate({
        rules: {
            fdate: {
                required: false,
                DateFormat: true,
                maxDate: true,
            },
            tdate: {
                required: true,
                DateFormat: true,
                maxDate: true,
                minDate: true,
            },
            "loc_dc_id[]": {
                required: true,
            },
        },
        submitHandler: function (form) {
            var form = $('#exportPOForm');
            window.location = form.attr('action') + '?' + form.serialize();
            $('.close').click();
        }
    });
    $('#POReportForm').validate({
        rules: {
            pofdate: {
                required: false,
                DateFormat: true,
                maxDate: true,
            },
            potdate: {
                required: false,
                DateFormat: true,
                maxDate: true,
                minDate: true,
            },
            "loc_dc_id[]":{
                required: true,
            },
        },
        submitHandler: function (form) {
            var form = $('#POReportForm');
            window.location = form.attr('action') + '?' + form.serialize();
            $('.close').click();
        }
    });
 $('#POGSTReportForm').validate({
  rules: {
    gstfdate: {
        required: false,
        DateFormat: true,
        maxDate: true,
    },
    gsttdate: {
        required: false,
        DateFormat: true,
        maxDate: true,
        minDate: true,
    },
    "loc_dc_id[]":{
        required: true,
    },
},
submitHandler: function (form) {
    var form = $('#POGSTReportForm');
    window.location = form.attr('action') + '?' + form.serialize();
    $('.close').click();
}
});

  /*  $('#importpoform').submit(function (e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $(this).attr('action');
        $('.spinnerQueue').show();
        $('.close').trigger('click');
        $("#uploadfile").attr("disabled", "disabled");
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            beforeSend: function (xhr) {
                $('.spinnerQueue').show();
                $('.close').trigger('click');
            },
            success: function (data) {
                $('.spinnerQueue').hide();
                $('.close').trigger('click');
                if(data.status == 200){
                    window.open('/'+data.url);
                }else if(data.status == 400){
                    if(data.url !="")
                        window.open('/'+data.url);
                    else
                        alert(data.message);
                }else{
                    alert('Server Error!');
                }
                $("#uploadfile").removeAttr("disabled");
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
                $(".alert-success").fadeOut(30000)
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
    $("#importpo").on('hide.bs.modal', function () {
        $('#importpoform')[0].reset();
        $("#uploadfile").removeAttr("disabled");
    });*/
});
$('a.link').on('click touchend', function(e) {
  var link = $(this).attr('href');
  window.location = link;
});
$('#exportpo').on('hidden.bs.modal', function (e) {
  // do something when this modal window is closed...
      $('#fdate').val("");
      $('#tdate').val("");
});
</script>
 <script type="text/javascript">
              $(document).ready(function(){             
               var dateFormat = "dd-mm-yy";
                    
                          from = $( "#from_date" ).datepicker({
                            dateFormat: 'dd-mm-yy',
                            maxDate:0,
                            onSelect: function () {
                              var select_date = $(this).datepicker('getDate');
                              var nextdayDate = getNextDay(select_date);
                              $('#to_date').datepicker('option', 'minDate', select_date);
                          }
                            //changeMonth: true,          
                          }),
                          to = $( "#to_date" ).datepicker({
                                  dateFormat: 'dd-mm-yy',
                                   maxDate: '+0D',
                                //changeMonth: true,        
                          });
      
            });
        function getNextDay(select_date) {
        select_date.setDate(select_date.getDate());
        var setdate = new Date(select_date);
        var nextdayDate = zeroPad(setdate.getDate(),2) + '-' + zeroPad((setdate.getMonth() + 1), 2) + '-' + setdate.getFullYear();
        return nextdayDate;
    }

    function zeroPad(num, count) {
        var numZeropad = num + '';
        while (numZeropad.length < count) {
            numZeropad = "0" + numZeropad;
        }
        return numZeropad;
    }

  $("#filter_button").click(function() {

    var csrf_token = $('#csrf-token').val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    var sup_name = $('#sup_name').val();
    if(from_date == ''){
        alert("Please select from date");
        return false;
    }
    else if(to_date == ''){
         alert("Please select to date");
         return false;
    }else{
    window.location.href = '/vendor/payments/57219?from_date='+from_date+'&to_date='+ to_date + '&sup_name='+ sup_name;
    }
    
    });
$('#downloadReport').validate({
  rules: {
    fdate: {
        required: false,
        DateFormat: true,
        maxDate: true,
    },
    tdate: {
        required: false,
        DateFormat: true,
        maxDate: true,
        minDate: true,
    },
},
submitHandler: function (form) {
    var form = $('#downloadReport');
    window.location = form.attr('action') + '?' + form.serialize();
    $('.close').click();
}
});



</script>
@stop
@extends('layouts.footer')
