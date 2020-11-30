@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
@if(isset($notification))
<span id="notification"><div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>{{ $notification }}</div></div></span>
@endif
<span id="success_message"></span>
<div id="loadingmessage" class=""></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height: auto;">
            <div class="portlet-title">
                <div class="caption">DiMiCi Report</div>
                <div class="actions">
                    <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document"class="btn green-meadow">Upload Di Mi Ci Report</a>
                </div>
            </div>
            <div class="portlet-body">
                <!--Start of filters-->
                <div id="dimici_filter_div">
                    {{ Form::open(array('url' => '/dimici/downloadreport', 'id' => 'download_dimici_report'))}}
                    <div class="row">
                        <div class="col-md-3">                        
                            <div class="form-group">
                                <select name="dc_name" id="dc_name" class="form-control dc_reset select2me" placeholder="DC Names" align="right">
                                    @foreach ($warehouses as $dc_name)
                                    <option value="{{ $dc_name->le_wh_id }}">{{ $dc_name->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">                        
                            <div class="form-group">
                                <div class="input-icon input-icon-sm right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" name="transac_date_from" id="transac_date_from" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('dmsReportLabels.filter_from_date') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">                        
                            <div class="form-group">
                                <div class="input-icon input-icon-sm right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" name="transac_date_to" id="transac_date_to" class="form-control end_date dp" value="" autocomplete="off" placeholder="{{ trans('dmsReportLabels.filter_to_date') }}">
                                </div>
                            </div>                        
                        </div>
                        <div class="col-md-2">                        
                                <label style="margin: 10px;">
                                    <input type="checkbox" id="cfc_check" value="1" name = "cfc_check">&nbsp; CFC To Buy > 1
                                </label>
                        </div>
                        <div class="col-md-3">
                            <!--<input type="button" value="Trigger Report" class="btn green-meadow" onclick="callTrigger();">-->
                            <input type="submit" value="Trigger Report" class="btn green-meadow">
                            <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
                <!--End of filters-->
                <!--Start of upload excel-->
                <div class="modal fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Upload Di Mi Ci Report</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row text-center">
                                    <div class="col-md-6">
                                        <input type="file" name="upload_dimici" id="upload_dimici" value="" class="form-control" />
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button"  class="btn green-meadow" id="dimici-upload-button" onclick="uploadReport()">Upload Di Mi Ci Report</button>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="text-danger"><u>Note:</u> Warehouse Id should be at first row and SKU, CFC To Buy & PM should be at second row 2nd, 34th and 38th columns respectively and should not be empty in excel sheet.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End of upload excel-->
            </div>
        </div>
    </div>
</div>

@stop

@section('userscript')
<style type="text/css">
    #loadingmessage{ z-index: 9999999999 !important; position: relative; top: 250px !important;}

    /*/ Absolute Center Spinner /*/
    .loading {
        position: fixed;
        z-index: 999;
        height: 2em;
        width: 2em;
        overflow: show;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    /*/ Transparent Overlay /*/
    .loading:before {
        content: '';
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.3);
    }

    /*/ :not(:required) hides these rules from IE9 and below /*/
    .loading:not(:required) {
        /*/ hide "loading..." text /*/
        font: 0/0 a;
        color: transparent;
        text-shadow: none;
        background-color: transparent;
        border: 0;
    }

    .loading:not(:required):after {
        content: '';
        display: block;
        font-size: 10px;
        width: 1em;
        height: 1em;
        margin-top: -0.5em;
        -webkit-animation: spinner 1500ms infinite linear;
        -moz-animation: spinner 1500ms infinite linear;
        -ms-animation: spinner 1500ms infinite linear;
        -o-animation: spinner 1500ms infinite linear;
        animation: spinner 1500ms infinite linear;
        border-radius: 0.5em;
        -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
    }

    /*/ Animation /*/

    @-webkit-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-moz-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-o-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
</style>
<!--Ignite UI Required Combined CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#notification").fadeOut(9000);
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
    
    function callTrigger(){
        var cfc_check = $('#cfc_check').is(':checked');
        var token = $("#token_value").val(),
        startDate = $("#transac_date_from").val(),
        endDate = $("#transac_date_to").val(),
        dcName = $("#dc_name").val(),
        formData = new Array();
        formData.push({"startDate": startDate, "endDate": endDate, 'dcName':dcName,'cfc_check':cfc_check});
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
            url: "/dimici/grid?_token=" + token,
            data: "filterDetails=" + JSON.stringify(formData),
            success: function (data)
            {
                console.log(data);
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+data+'</div></div>');
                $(".alert-success").fadeOut(40000);
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }
    
    function uploadReport() {
        var stn_Doc = $("#upload_dimici")[0].files[0],
            formData = new FormData(),
            token = $("#token_value").val(),
            ext = stn_Doc.name.split('.').pop().toLowerCase();
        if($.inArray(ext, ['xls', 'xlsx']) == -1) {
            alert("Please choose a valid excel file (.xls or .xlsx)");
            return false;
        }
        if (typeof stn_Doc == 'undefined')
        {
            alert("Please choose the file to upload");
            return false;
        }
        formData.append('upload_doc', stn_Doc);
        $.ajax({
            type: "POST",
            url: "/dimici/uploadreport?_token=" + token,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#loadingmessage').addClass("loading");
            },
            complete: function () {
                $('#loadingmessage').removeClass("loading");
            },
            success: function (data)
            {
                var splidData = data.split('-');
                $("#upload_dimici").val("");
                $('#upload-document').modal('toggle');
                $("#success_message").html('<div class="flash-message"><div class="alert alert-' + splidData[0] + '"><button type="button" class="close" data-dismiss="alert"></button>' + splidData[1] + '</div></div>');
                $(".alert-" + splidData[0]).fadeOut(40000);
            }
        });
    }
</script>
@stop
@extends('layouts.footer')