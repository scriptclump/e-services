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
                <div class="caption">{{ trans('gstReportLabels.heading') }}</div>
                <div class="actions">

                </div>
            </div>
            <div class="portlet-body">
                <!--Start of filters-->
                <div id="dimici_filter_div">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <select id="le_id" name="le_id" class="form-control">
                                    @foreach($warehouse as $data)
                                        <option value="{{$data->le_wh_id}}">{{$data->display_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">                        
                            <div class="form-group">
                                <div class="input-icon input-icon-sm right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" name="transac_date_from" id="transac_date_from" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('gstReportLabels.from_date') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">                        
                            <div class="form-group">
                                <div class="input-icon input-icon-sm right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" name="transac_date_to" id="transac_date_to" class="form-control end_date dp" value="" autocomplete="off" placeholder="{{ trans('gstReportLabels.to_date') }}">
                                </div>
                            </div>                        
                        </div>
                        <div class="col-md-3">
                            <input type="button" value="{{ trans('gstReportLabels.submit') }}" class="btn green-meadow" onclick="callTrigger();">
                            <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                        </div>
                    </div>
                </div>
                <!--End of filters-->
            </div>
        </div>
    </div>
</div>

@stop

@section('userscript')
<!--Ignite UI Required Combined CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    $(document).ready(function () {
        $('#transac_date_from').datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: 0,
            onSelect: function () {
                var select_date = $(this).datepicker('getDate');
                var nextdayDate = getNextDay(select_date);
                $('#transac_date_to').datepicker('option', 'minDate', nextdayDate);
            }
        });
        $('#transac_date_to').datepicker({
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
                startDate = $("#transac_date_from").val(),
                endDate = $("#transac_date_to").val(),
                formData = new Array();
        formData.push({"startDate": startDate, "endDate": endDate});
        if (startDate == "")
        {
            alert("Please select from date");
            $("#transac_date_from").focus();
            return false;
        }
        if (endDate == "")
        {
            alert("Please select to date");
            $("#transac_date_to").focus();
            return false;
        }
        $.ajax({
            type: "GET",
            url: "/gstreports/getoutwardreport?_token=" + token,
            data: "filterDetails=" + JSON.stringify(formData),
            success: function (data)
            {
                console.log(data);
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>' + data + '</div></div>');
                $(".alert-success").fadeOut(40000);
                $("#transac_date_from").val('');
                $("#transac_date_to").val('');
            },
            error: function (data)
            {
                console.log(data);
            }
        });
    }
</script>
@stop
@extends('layouts.footer')
