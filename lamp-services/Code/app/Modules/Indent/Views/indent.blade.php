@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/indents/indent">Indents</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Order Indent</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">INDENT</div>
                <div class="tools"><a href="javascript:void(0);" id="toggleFilter"><i class="fa fa-filter"></i></a></div>
            </div>
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />		 

            <div class="portlet-body">		   
                <div class="row">
                    <div class="col-md-12">&nbsp;
                    </div>
                </div>	 
                <div class="row">
                    <div class="col-md-12"><a  type="button" href="/indents/createIndent" class="btn green-meadow" id="toggleFilter">CREATE INDENT</a>
                    </div>
                </div>	 
                <div class="row">
                    <div class="col-md-12">
                        <table id="pickList" class="table table-striped table-bordered table-hover"></table>
                    </div>
                </div>	

            </div>
        </div>
    </div>				


</div>
</div>
</div>
@stop

@section('style')
<style type="text/css">
    .centerAlignment { text-align: center;}
    #pickList_SNo {text-align:center !important;}
    #pickList_pickingID {text-align:center !important;}
    #pickList_pickingDate {text-align:center !important;}
    #pickList_pickingLocation {text-align:center !important;}
    #pickList_Status {text-align:center !important;}
    #pickList_Actions {text-align:center !important;}

    .captionmarg{margin-top:15px;}
    .sortingborder{border-bottom:1px solid #eee;border-top:1px solid #eee; padding:10px 0px; margin-top:15px;}

    .sorting a{ list-style-type:none !important;text-decoration:none !important;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#ddd !important;}

</style>
@stop

@section('userscript')
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.theme.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.css" rel="stylesheet" />

<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.core.js"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.lob.js"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>

<script type="text/javascript">

$(document).ready(function () {
    getOrderIndentList();
    $('#fdate').datepicker();
    $('#tdate').datepicker();

    $("#toggleFilter").click(function () {
        $("#filters").toggle("slow", function () {
        });
    });
});
</script>
@stop
