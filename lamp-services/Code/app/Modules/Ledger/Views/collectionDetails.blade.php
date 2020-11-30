@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Collection Details</li>
        </ul>
    </div>
</div>




<div class="portlet light portlet-fit portlet-datatable bordered">
    <div class="portlet-title">
        <div class="caption uppercase">Collection Details</div>
        <div class="tools uppercase">&nbsp;</div>
    </div>


    <div class="portlet-body">



        <div class="row">
            <div style="display:none;" id="ajaxResponse" class="alert alert-success">
            </div>
            <div class="col-md-3">		
                <div class="form-group">
                    <label>Submitted By</label>
                    <select class="form-control" name="del_exec" id="del_exec">
                        <option value="0">All</option>
                        @foreach($deliveryUsers as $User)
                        <option value="{{ $User->user_id }}">{{ $User->firstname.' '.$User->lastname }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">      
                <div class="form-group">
                    <label>Delivery From Date</label>
                    <div class="input-icon right">
                        <i class="fa fa-calendar"></i>
                        <input type="text" class="form-control" name="del_fdate" id="del_fdate" value=""/>
                    </div>
                </div>
            </div>
            <div class="col-md-3">      
                <div class="form-group">
                    <label>Delivery To Date</label>
                    <div class="input-icon right">
                        <i class="fa fa-calendar"></i>
                        <input type="text" class="form-control" name="del_tdate" id="del_tdate" value=""/>
                    </div>
                </div>
            </div>


            <div class="col-md-2">		
                <div class="form-group searchbutt">
                    <button class="btn green-meadow" id="collection_search">Search</button>
                    <button class="btn green-meadow pull-right" href="/payments/paymentReport" id="export_excel"><i class="fa fa-file-excel-o fa-lg"></i> Export</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="paymentsApprovalGrid"></table>
        </div>
    </div>

</div>

@include('Ledger::Form.paymentApprovalPopup')
@include('Ledger::Form.paymentHistoryPopup')
@stop

@section('userscript')

<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/admin/pages/scripts/payments/payments_grid.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/payments/approvalscript.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    function checkAll(ele) {
        var checkboxes = document.getElementsByClassName('ledger_chk');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }

    $('#del_fdate,#del_tdate').on('keydown',function(){return false;})

    $('#del_tdate').datepicker({maxDate: 0, dateFormat: 'dd-mm-yy'}).datepicker("setDate", new Date());
    $('#del_fdate').datepicker({maxDate: 0,
            dateFormat: 'dd-mm-yy'}).datepicker("setDate", new Date());

        $('#collection_search').on('click', function () {
            var Del_Exec = $('#del_exec').val();
            var Del_FDate = $('#del_fdate').val();
            var Del_TDate = $('#del_tdate').val();
            collectionDetails(Del_Exec, Del_FDate, Del_TDate);
        });
    $('#approve').on('click', function () {
        var approvedPayments = [];
        $.each($('.ledger_chk:checked'), function (key, val) {
            approvedPayments.push($(this).val());
        })
        if (approvedPayments.length == 0) {
            alert('Please select atleast one payment');
        } else {
            if (confirm('Are you sure do you want to Approve the payments'))
            {
                $.ajax({
                headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                        url: '/payments/approvePayments',
                        type: "POST",
                        data: {'ids': approvedPayments},
                        dataType: 'json',
                        success: function (data) {
                        if (data.status == 200) {
                        $('#ajaxResponse').removeClass('text-danger').addClass('text-success').html(data.message).show();
                        } else {
                        $('#ajaxResponse').removeClass('text-success').addClass('text-danger').html(data.message).show();
                        }
                        },
                        error: function (response) {
                        $('#ajaxResponse').html('Unable to approve');
                        }
                    });
            }
        }
        });
    $(document).on('click', ".remittanceDetail", function () {
        var Index = $(this).parents('tr');
        $(".paymentsApprovalGrid").igHierarchicalGrid("expand", Index);
    });
    $(document).on('click', ".pmtApprv", function () {
        var remittance_id = $(this).attr('data-remittace');
        var status = $(this).attr('data-status');
        $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/remittance/getApprlForm/' + remittance_id + '/' + status,
                type: "POST",
                data: {},
                success: function (response) {
                $('#paymentPupupId').html(response);
                },
                error: function (response) { }
        });
    });
    $(document).on('click', ".remittanceHistoryDetail", function () {
        var remittance_id = $(this).parents('tr').attr('data-id');
        $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/payments/getApprovalHistory/Payment/' + remittance_id,
                type: "POST",
                data: {},
                success: function (response) {
                $('#pmtHistoryBody').html(response);
                },
                error: function (response) { }
        });
    });
    $(document).on('click', "#export_excel", function () {
        var del_exec = $('#del_exec').val();
        var del_fdate = $('#del_fdate').val();
        var del_tdate = $('#del_tdate').val();
        var url = '/payments/collectionReport?executive=' + del_exec + '&fdate=' + del_fdate + '&tdate=' + del_tdate;
        window.location.href = url;
    });

</script>


@stop

@section('style')
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" /><style type="text/css">
    .searchbutt{ margin-top: 23px; }	
    .checkboxmarleft{margin-left:25px !important; text-align:center !important;}
.textRightAlign {text-align: right;}
</style>
@stop

