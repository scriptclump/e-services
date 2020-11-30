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
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Purchase Returns </div>
                <div class="actions">
                    @if(isset($featureAccess['assignPickerFeature']) && $featureAccess['assignPickerFeature'] && ($filter_status== 'picklist'||$filter_status== 'rtd'))
                    <a class="btn btn-success" href="#prPicklist" data-toggle="modal" id="prprint">Print Picklist</a>
                    @endif
                    <?php /* @if(isset($featureAccess['printPicklistFeature']) && $featureAccess['createPrFeature'] && $filter_status== 'rtd')
                    <a class="btn btn-success" data-toggle="modal" id="picklist">Print Picklist</a>
                    @endif */ ?>                    
                    @if(isset($featureAccess['exportFeature']) && $featureAccess['exportFeature'])
                    <a type="button" id="" href="#exportpr" data-toggle="modal" class="btn green-meadow">Export PR</a>
                    <a type="button" id="" href="#importpr" data-toggle="modal" class="btn green-meadow">Import PR</a>
                    @endif
                    @if(isset($featureAccess['createPrFeature']) && $featureAccess['createPrFeature'])
                    <a href="/pr/create" class="btn btn-success">Create PR</a>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="row sortingborder">
                    <div class="col-md-6">
                        <div class="caption">
                            <span class="caption-subject bold font-blue">Filter By:</span>
                            <span class="caption-helper sorting">                                
                                <a href="{{$app['url']->to('/')}}/pr/index/initiated" class="{{($filter_status == 'initiated') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="PR Created">Initiation ({{$poCounts['initiated']}})</a> &nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/pr/index/picklist" class="{{($filter_status == 'picklist') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Picklist">Picklist ({{$poCounts['created']}})</a> &nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/pr/index/rtd" class="{{($filter_status == 'rtd') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Ready To Dispatch">RTD ({{$poCounts['picklist']}})</a> &nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/pr/index/verification" class="{{($filter_status == 'verification') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Verification">Verify & Dispatch ({{$poCounts['RTD']}})</a> &nbsp;&nbsp;
                                <?php /*<a href="{{$app['url']->to('/')}}/pr/index/dispatch" class="{{($filter_status == 'dispatch') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Stock Dispatch">Verify & Dispatch ({{$poCounts['verified']}})</a> &nbsp;&nbsp; */?>
                                <a href="{{$app['url']->to('/')}}/pr/index/finance" class="{{($filter_status == 'finance') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Finance">Finance ({{$poCounts['dispatched']}})</a> &nbsp;&nbsp;
                                <br/><span style="padding-left:84px;"></span>
                                <a href="{{$app['url']->to('/')}}/pr/index/cancelled" class="{{($filter_status == 'cancelled') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Cancelled">Cancelled ({{$poCounts['canceled']}})</a> &nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/pr/index/completed" class="{{($filter_status == 'completed') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Completed">Completed ({{$poCounts['completed']}})</a> &nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/pr/index/total" class="{{($filter_status == 'total') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Total">Total ({{$poCounts['Total']}})</a> &nbsp;&nbsp;
                            </span>
                        </div>
                    </div>
                </div>
                <div style="display:none; margin-top:5px;" id="ajaxResponse" class="col-md-12 alert alert-danger"></div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        &nbsp;
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="scroller" style="height: 400px;">
                            <table id="prList" class="table  table-advance table-hover"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($featureAccess['exportFeature']) && $featureAccess['exportFeature'])
<div class="modal modal-scroll fade in" id="exportpr" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Export PR</h4>
            </div>
            <div class="modal-body">
                <form id="exportPRForm" action="/pr/downloadPRExcel" class="text-center" method="post">
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div style="display:none;" id="error-msg" class="alert alert-danger"></div>                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="prfdate" name="fdate" class="form-control" placeholder="From Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="prtdate" name="tdate" class="form-control" placeholder="To Date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: default current month data will download
                        </div>
                    </div>
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
<div class="modal modal-scroll fade in" id="importpr" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Import PR</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="importprform" action="/pr/importPRExcel" class="text-center" method="post" enctype="multipart/form-data">
                        <div class="row">

                            <div class="col-md-12" align="center">
                                <div style="display:none;" id="error-msg" class="alert alert-danger"></div>                        
                                <div class="form-group">
                                    <div class="fileUpload btn green-meadow"> <span id="up_text">Choose PR Template</span>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="file" name="prfile" id="pofile" class="form-control upload"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" id="uploadfile" class="btn green-meadow">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12 text-center">
                        <a href="/pr/downloadprimport" class="btn green-meadow">Download PR Template</a>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@endif
@stop

@section('userscript')
@include('PurchaseReturn::printPicklistPopup');

<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/poscript.js') }}" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function () {
    purchaseReturnGrid('{{$filter_status}}',0);
    $('#prfdate').datepicker({
        maxDate: 0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
            $('#prtdate').datepicker('option', 'minDate', seldayDate);
        }
    });

    $('#prtdate').datepicker({
        maxDate: 0,
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
        var fdate = new Date($('#prfdate').val());
        var myDate = new Date(value);
        return this.optional(element) || myDate >= fdate;
    },
            "should not be less than from date"
            );
    $('#exportPRForm').validate({
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
            var form = $('#exportPRForm');
            window.location = form.attr('action') + '?' + form.serialize();
            $('.close').click();
        }
    });
        $("#exportpr").on('hide.bs.modal', function () {
        $('#exportPRForm')[0].reset();
    });
});
$('#prprint').on('click', function () {
    var status = getStatusVal();
    var selected = getChkVal();
    if (selected.length > 0) {
        var printPL_status = 'success';
    } else {
        $('#ajaxResponse').html('Please select at least one order.').show();
        return false;
    }
});
function checkAll(ele) {
    var checkboxes = document.getElementsByTagName('input');
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

function getStatusVal() {
    var selected = [];
    $("input[name='chk[]']").each(function () {
        if ($(this).val() != 'on') {
            if ($(this).prop('checked') == true) {
                var chkId = $(this).val();
                var statusVal = $('#' + chkId).val();
                selected.push(statusVal);
            }
        }
    });

    return selected;
}
function getChkVal() {
    var selected = [];

    $("input[name='chk[]']").each(function () {
        if ($(this).val() != 'on') {
            if ($(this).prop('checked') == true) {
                selected.push($(this).val());
            }
        }
    });

    return selected;
}

$('#prPicklistForm').validate({
    rules: {
        picked_by: {
            required: true
        },
        /* pickdate: {
         required: true
         } */
    },
    submitHandler: function (form) {
        var selected = getChkVal();
        var status = getStatusVal();
        var pickedBy = $('#picked_by').val();
        var docArea = $('#doc_area').val();
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/pr/savepicklist",
            type: "POST",
            data: {ids: selected, statusCodes: status, pickedBy: pickedBy},
            dataType: 'json',
            beforeSend: function () {
                $('#loader1').show();
            },
            success: function (response) {
                if (response.status == 200) {
                    $('.loderholder').hide();
                    window.open('/pr/printPicklist', '_blank');
                    window.location.href = '/pr/index';
                } else {
                    $('#ajaxResponse').html(response.message).show();
                    $('.loderholder').hide();
                }
            },
            error: function (response) { }
        });
    }
});
 

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

$('#importprform').submit(function (e) {
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
});

</script>
@section('style')
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .fa-eye{color:#3598dc !important;}
    .fa-print{color:#3598dc !important;}
    .fa-download{color:#3598dc !important;}
    /*.portlet > .portlet-title { margin-bottom:0px !important;}*/
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


    .favfont i{font-size:18px !important;}
    .actionss{padding-left: 22px !important;}

    .sortingborder{border-bottom:1px solid #eee;border-top:1px solid #eee; padding:10px 0px;}

    .sorting a{ list-style-type:none !important;text-decoration:none !important;font-size: 12px;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#676767 !important;}
    .ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{border-spacing:0px !important;}
    .ui-datepicker .ui-datepicker-prev .ui-icon, .ui-datepicker .ui-datepicker-next .ui-icon{
        color:#000 !important;
    }
    .centerAlignment { text-align: center;}
    .rightAlignment { text-align: right;}
    th.ui-iggrid-header:nth-child(6){
        text-align: right !important;
    }
#prList_pager_label{
    margin-left: -20%;
}
</style>
@stop
@stop
@extends('layouts.footer')
