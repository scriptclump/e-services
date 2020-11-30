@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<div id="loadingmessage" class=""></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height: auto;">
            <div class="portlet-title">
                <div class="actions">

                </div>
            </div>
            <div class="portlet-body">
                <!--Start of filters-->
                <form action="/financeReport" method="POST" id="report_form">
                <div id="dimici_filter_div">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                 <div class="caption">Reports</div>
                                <select id="report_type" name =  "report_type[]" class="form-control multi-select-search-box" multiple="multiple">
                                   
                                </select>
                            </div>
                        </div> 

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="caption">Business Units</div>
                                <select id="business_unit_id" name="business_unit_id" class="multi-select-search-box form-control dc_names_select">                                   
                                </select>
                            </div>
                        </div>  
                        <div class="col-md-2" style="padding-top:18px">                        
                            <div class="form-group">
                                <div class="input-icon input-icon-sm right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" name="from_date" id="from_date" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('gstReportLabels.from_date') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" style="padding-top:18px">                        
                            <div class="form-group">
                                <div class="input-icon input-icon-sm right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" name="to_date" id="to_date" class="form-control end_date dp" value="" autocomplete="off" placeholder="{{ trans('gstReportLabels.to_date') }}">
                                </div>
                            </div>                        
                        </div>
                        <div class="col-md-2" style="padding-top:18px">
                            <input type="button" value="{{ trans('gstReportLabels.submit') }}" class="btn green-meadow" onclick="callTrigger()" >
                            <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                        </div>
                    </div>
                </div>
            </form>
                <!--End of filters-->
            </div>
        </div>
    </div>
</div>

@stop
<style type="text/css">
    
.bu1{
    margin-left: 10px;
    font-size: 19px;
    color:#000000;
}
.bu2{
    margin-left: 20px;
    font-size: 18px;
    color:#1d1d1d;
}.bu3{
    margin-left: 30px;
    font-size: 16px;
    color:#3a3a3a;
}.bu4{
    margin-left: 40px;
    font-size: 14px;
    color:#535353;
}.bu5{
    margin-left: 50px;
    font-size: 13px;
    color: #6d6c6c;
}.bu6{
    margin-left: 60px;
    font-size: 11px;
    color:#868383;
}
</style>
@section('userscript')
<!--Ignite UI Required Combined CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/admin/pages/css/tasks.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<!-- <script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script> -->
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.full.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/addpromotion.js') }}" type="text/javascript"></script>


<script type="text/javascript">
    $(document).ready(function () {
        $('#from_date').datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: 0,
            onSelect: function () {
                var select_date = $(this).datepicker('getDate');
                var nextdayDate = getNextDay(select_date);
                $('#to_date').datepicker('option', 'minDate', nextdayDate);
            }
        });
        $('#to_date').datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: '+0D',
        });
    });

    function getNextDay(select_date) {
        select_date.setDate(select_date.getDate());
        var setdate = new Date(select_date);
        var nextdayDate = setdate.getFullYear() + '-' + zeroPad((setdate.getMonth() + 1), 2) + '-' + zeroPad(setdate.getDate(), 2);
        return nextdayDate;
    }

    function zeroPad(num, count) {
        var numZeropad = num + '';
        while (numZeropad.length < count) {
            numZeropad = "0" + numZeropad;
        }
        return numZeropad;
    }

    function callTrigger() {
        var token = $("#token_value").val(),
            startDate = $("#from_date").val(),
            endDate = $("#to_date").val(),
            business_unit=$('#business_unit_id').val();
        var report = $('#report_type > option:selected');
            formData = new Array();
        formData.push({"startDate": startDate, "endDate": endDate,"business_unit_id":business_unit,"report_type":report});
        if (startDate == "")
        {
            alert("Please Select From Date");
            $("#from_date").focus();
            return false;
        }
        if (endDate == "")
        {
            alert("Please Select To Date");
            $("#to_date").focus();
            return false;
        }
         if(report.length == 0){
             alert('Please Select Given Option');
             return false;
         }
        if (business_unit == "")
        {
            alert("Please Select Business Unit");
            $("#business_unit_id").focus();
            return false;
        }
        $("#report_form").submit();
    };

    $(function(){
    // Custom Title for The Dashboard
    var token=$('#csrf-token').val();
    $.ajax({
    type:'get',
    headers: {'X-CSRF-TOKEN': token},
    url:'/getbu',
    success: function(res){
        $('#report_type').append('<option value="1">Invoice Tax Report</option>');
        $('#report_type').append('<option value="2">Invoice HSN wise Report</option>');
        $('#report_type').append('<option value="3">Return Tax Report</option>');
        $('#report_type').append('<option value="4">Return HSN wise Report</option>');
        $('#report_type').append('<option value="5">Delivered HSN wise Report</option>'); 
        $('#business_unit_id').append('<option value=""></option>');
        $('#report_type')[0].sumo.reload();

        res.forEach(data=>{
            $('#business_unit_id').append(data);
        });
        $('#business_unit_id')[0].sumo.reload();
    }

    });
});
</script>
@stop
@extends('layouts.footer')