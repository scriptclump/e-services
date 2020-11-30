@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Account Receivables</li>
        </ul>
    </div>
</div>




<div class="portlet light portlet-fit portlet-datatable bordered">
    <div class="portlet-title">
        <div class="caption">Account Receivables</div>
        <div class="tools uppercase">&nbsp;</div>
    </div>


    <div class="portlet-body">



        <div class="row">
            <div style="display:none;" id="ajaxResponse" class="alert alert-success">
            </div>
            <div class="col-md-3">		
                <div class="form-group">
                    <label>Submitted By</label>
                    <select class="form-control select2me" name="del_exec" id="del_exec">
                        <option value="0">All</option>

                        @foreach($deliveryUsers as $User)
                        <option value="{{ $User->user_id }}">{{ $User->firstname.' '.$User->lastname }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">      
                <div class="form-group">
                    <label>Delivery From Date</label>
                    <div class="input-icon right">
                        <i class="fa fa-calendar"></i>
                        <input type="text" class="form-control" name="del_fdate" id="del_fdate" value=""/>
                    </div>
                </div>
            </div>
            <div class="col-md-2">      
                <div class="form-group">
                    <label>Delivery To Date</label>
                    <div class="input-icon right">
                        <i class="fa fa-calendar"></i>
                        <input type="text" class="form-control" name="del_tdate" id="del_tdate" value=""/>
                    </div>
                </div>
            </div>

            <div class="col-md-3">      
                <div class="form-group">
                    <label>Status</label>

                    <div class="input-icon right">
                        <select class="form-control" name="del_status" id="del_status">
                            <option value="0">All</option>
                            @foreach($remStatusArr as $Status_Value=>$Status_Name)
                            <option value="{{ ($Status_Name=='Finance Approved') ? 1 :$Status_Value }}">{{ $Status_Name }}</option>
                            @endforeach
                        </select>

                    </div>


                </div>
            </div>

            <div class="col-md-2">		
                <div class="form-group searchbutt">
                    <button class="btn green-meadow" id="payment_search">Search</button>
                    <button class="btn green-meadow pull-right" href="/payments/paymentReport" id="export_excel"><i class="fa fa-file-excel-o fa-lg"></i> Export</button>
                </div>
            </div>
        </div>
        <div class="row showConsolidate" style="display:none">
            <div class="col-md-3"><strong>Total Amount:</strong> <span class="total_collected_amt"></span></div>
            <div class="col-md-3"><strong>By Cash:</strong> <span class="total_by_cash"></span></div>
            <div class="col-md-3"><strong>By Cheque:</strong> <span class="total_by_cheque"></span></div>
            <div class="col-md-3"><strong>By Online:</strong> <span class="total_by_online"></span></div>
            <div class="col-md-3"></div>
            <div class="col-md-3"><strong>By UPI:</strong> <span class="total_by_upi"></span></div>
            <div class="col-md-3"><strong>By eCash:</strong> <span class="total_by_ecash"></span></div>
            <div class="col-md-2"><strong>By POS:</strong> <span class="total_by_pos"></span></div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <button type="button" href="#pmtApprvlPopup" data-toggle="modal" class="btn green-meadow pmtApprv" data-status="57051" id="submit_to_finance">Submit To Finance</button>
            </div>
        </div>    
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="paymentsApprovalGrid" id="paymentsApprovalGrid"></table>
        </div>
    </div>

    <!--<div class="col-md-4">		
    <div class="form-group">
    <button class="btn green-meadow" id="approve">Approve</button>
    </div>
    </div>-->

    <?php /* ?>
      <div class="row" id="approval_div">
      <div class="col-md-3">
      <div class="form-group">
      <select name="next_status" id="next_status" class="form-control">
      <option>Select</option>
      @foreach ($approvalOptions as $eachOptionKey => $eachOptionValue)
      <option value="{{ $eachOptionKey }}">{{ $eachOptionValue }}</option>
      @endforeach
      </select>
      <input type="hidden" name="current_status" id="current_status" value="" />
      </div>
      </div>
      <div class="col-md-6">
      <textarea name="approval_comment" rows="3" id="approval_comment" class="form-control" required></textarea>
      </div>
      <div class="col-md-3">
      <input type="submit" id="approval_submit" class="btn btn-primary" value="Submit">
      </div>
      <div id="loader_div" class="loader-outer display_div">
      <div class="loader" style=""></div>
      </div>
      </div>
      <?php */ ?>

</div>

@include('Ledger::Form.paymentApprovalPopup')
@include('Ledger::Form.paymentHistoryPopup')
@stop

@section('userscript')

<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
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


    $('#del_tdate').datepicker({
        maxDate: 0,
        dateFormat: 'dd-mm-yy'
    }).datepicker("setDate", new Date());
    $('#del_fdate').datepicker({
        maxDate: 0,
        dateFormat : 'dd-mm-yy'
    }).datepicker("setDate", new Date());

        $('#payment_search').on('click', function () {
            var Del_Exec = $('#del_exec').val();
            var Del_FDate = $('#del_fdate').val();
            var Del_TDate = $('#del_tdate').val();
            var Status = $('#del_status').val();
            paymentsApproval(Del_Exec, Del_FDate, Del_TDate, Status);
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
        var status = $(this).attr('data-status');
        
        if(status==57055 || status==57052) {
            var remittance_id = $(this).attr('data-remittace');
        } else {
            
            var remittance_id = $('input[name="chk[]"]:checked').map(function() { 
                                    return this.value; 
                                }).get().join(',');
        }

        $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/remittance/getApprlForm',
                type: "POST",
                data: {remittance_id:remittance_id,status:status},
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
        var url = '/payments/paymentReport?executive=' + del_exec + '&fdate=' + del_fdate + '&tdate=' + del_tdate;
        window.location.href = url;
    });

    $(document).on('keyup', "#submitted_amount,#fuel,#other_vehicle,#short,#coins_on_hand,#notes_on_hand,#used_expenses", function () {

        var current_status = $('#current_status').val();


        if(current_status == 57055) {

            var original_amount = $('#submitted_amount').attr('data-amount');
            var current_val = parseInt($('#submitted_amount').val()) || 0;
            var fuel = parseInt($('#fuel').val()) || 0;
            var vehicle = parseInt($('#other_vehicle').val()) || 0;
            var short = parseInt($('#short').val()) || 0;

            $('.net_diffrence').html(original_amount-current_val-fuel-vehicle-short);

        }

        if(current_status == 57051) {

            var ecash_val = parseInt($('.by_ecash').html()) || 0;
            var by_cheque_val = parseInt($('.by_cheque').html()) || 0;
            var by_upi_val = parseInt($('.by_upi').html()) || 0;
            var by_online_val = parseInt($('.by_online').html()) || 0;
            var actual_amt_rem = parseInt($('.actual_amount_rem').html()) || 0;

            var fuel = parseInt($('.fuel').html()) || 0;
            var vehicle = parseInt($('.vehicle').html()) || 0;
            var short = parseInt($('.short').html()) || 0;

            var submitted_amount = parseInt($('#submitted_amount').val()) || 0;
            var submittable_amount = parseInt($('#submitted_amount').attr('data-amount'));
            var coins_on_hand = parseInt($('#coins_on_hand').val()) || 0;
            var notes_on_hand = parseInt($('#notes_on_hand').val()) || 0;
            var used_expenses = parseInt($('#used_expenses').val()) || 0;

            $('.net_diffrence').html((submittable_amount+submitted_amount)-actual_amt_rem-ecash_val-by_cheque_val-by_upi_val-by_online_val-fuel-vehicle-short-coins_on_hand-notes_on_hand-used_expenses);

        }

    });

    $(document).on('keyup', ".denom_input", function () {
        

        var this_val = parseInt($(this).val()) || 0;

        if($(this).val()>=0) {
            
            var currency = $(this).attr('data-value');

            var value = parseInt(currency) * this_val;

            $('#'+currency+'_input_result').html(value);

            var total = getDenominations();

            $('.denom_total').html(total)
        }

    });

    function getDenominations() {
        var total = 0;    

        $('.denom_input').each(function(i){

            total=total+parseInt($(this).attr('data-value')*$(this).val());
        
        });
    
        return total;
    }

    function getDenominationJson() {

        var json_val = {};

        $('.denom_input').each(function(){

            json_val[$(this).attr('data-value')] = $(this).val();

        });

        return json_val;
    }


    $('#submit_to_finance').on('click', function() {
        if($('input[name="chk[]"]:checked').length<=0) {

            alert('Please check atleast one remittance');
            return false;
        }
    });

</script>


@stop

@section('style')
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" /><style type="text/css">
    .searchbutt{ margin-top: 23px; }	
    .checkboxmarleft{margin-left:25px !important; text-align:center !important;}
    .rightAlignment { text-align: right; padding-right: 5px;}
    .leftAlignment { padding-left: 5px;}
</style>
@stop

