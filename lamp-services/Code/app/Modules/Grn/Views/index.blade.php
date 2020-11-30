@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>GRN</li>
        </ul>
    </div>
</div>
<div class="portlet light tasks-widget">
    <div class="portlet-title">
        <div class="caption">Manage GRN</div>
        <div class="actions">
            @if(isset($featureAccess['createFeature']) && $featureAccess['createFeature'])
            <a href="/grn/create" class="btn btn-success">Create GRN</a>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <div class="caption captionmarg">
                    <span class="caption-subject bold font-blue"> Filter By :</span>
                    <span class="caption-helper sorting">
                        <a href="{{$app['url']->to('/')}}/grn/index/all" class="{{($status == 'all' || $status == '') ? 'active' : 'inactive'}}">All (<span id="allorders">{{$counts['allCount']}}</span>)</a> &nbsp;
                        <a href="{{$app['url']->to('/')}}/grn/index/approved" class="{{($status == 'approved') ? 'active' : 'inactive'}}">Approved (<span id="allorders">{{$counts['approvedCount']}}</span>)</a> &nbsp;
                        <a href="{{$app['url']->to('/')}}/grn/index/notapproved" class="{{($status == 'notapproved') ? 'active' : 'inactive'}}">Not Approved (<span id="allorders">{{$counts['notapprovedCount']}}</span>)</a> &nbsp;
                        <a href="{{$app['url']->to('/')}}/grn/index/invoiced" class="{{($status == 'invoiced') ? 'active' : 'inactive'}}">Invoiced (<span id="allorders">{{$counts['invoicedCount']}}</span>)</a> &nbsp;
                        <a href="{{$app['url']->to('/')}}/grn/index/notinvoiced" class="{{($status == 'notinvoiced') ? 'active' : 'inactive'}}">Not Invoiced (<span id="allorders">{{$counts['notinvoicedCount']}}</span>)</a> &nbsp;
                    </span>
                    <div class="text-right" style="float: right; font-size:11px;"><b>* All Amounts in <i class="fa fa-inr" aria-hidden="true"></i></b></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">&nbsp;
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table id="grn" class="table table-striped table-bordered table-hover"></table>
            </div>
        </div>
    </div>
</div>
@stop

@section('style')
<style type="text/css">


.ui-iggrid .ui-iggrid-headertable{
    border-spacing: 0px !important;
}

    .fa-eye {
        color: #3598dc !important;
    }
    .fa-print {
        color: #3598dc !important;
    }
    .fa-download {
        color: #3598dc !important;
    }

    .centerAlignment { text-align: center;}
    .rightAlignment { text-align: right;}

    .captionmarg{margin-top:15px;}
    .sortingborder{border-bottom:1px solid #eee;border-top:1px solid #eee; padding:10px 0px; margin-top:15px;}

    .sorting a{ list-style-type:none !important;text-decoration:none !important;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#ddd !important;}

    .SumoSelect > .optWrapper > .options li label {
    white-space: pre-wrap !important;
    word-wrap: break-word !important;
    width: 250px !important;
    text-align: left;
    }
#grn_pager_label{
    margin-left: -20%;
} 
</style>
@stop

@section('userscript')
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.theme.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.core.js"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.lob.js"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>

<script type="text/javascript">
function filterGrn() {
    var formData = $('#frm_grn').serialize();
    var filterURL = "/grn/getgrn?" + formData;

    $("#grn").igGrid({
        dataSource: filterURL,
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
    getGrnList('{{$status}}');
    $('#fdate').datepicker({
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
    //$('#fdate').datepicker();
    $('#tdate').datepicker();

    $("#toggleFilter").click(function () {
        $("#filters").toggle("slow", function () {
        });
    });
});
</script>
@stop
