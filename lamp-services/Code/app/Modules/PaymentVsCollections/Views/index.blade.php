@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<ul class="page-breadcrumb breadcrumb">
    <li><a href="/">{{trans('PaymentVsCollection.dashboard_heads.heads_home')}}</a><i class="fa fa-angle-right" aria-hidden="true"></i>
    </li><li><span class="bread-color">{{trans('PaymentVsCollection.dashboard_heads.today_reports')}}</span><i class="fa fa-angle-right" aria-hidden="true"></i></li>
    <span>
</ul>

<div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">                
                    <div class="caption">Payments Vs Collections Report</div>
                </div>
                <!-- <form id="getledgerReportForm">      -->
                    <div class="row " style="margin-top:14px">
                    <div class="col-md-4" style="">
                      <div class ="caption text-center font-weight-bold text-uppercase">Payments</div>
                            <div class="customDateWidth">
                                <div class="form-group" id="customDatePickerZone">
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input type="text" class="form-control" name="PayfromDate" id="PayfromDate" placeholder = "From Date" autocomplete="Off" required/>
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="form-control" name="PaytoDate" id="PaytoDate" placeholder = "To Date" autocomplete="Off" required/>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-4" style="">
                        <div class ="caption text-center font-weight-bold text-uppercase">Collections</div>
                            <div class="customDateWidth" >
                                <div class="form-group" id="customDatePickerZone">
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input type="text" class="form-control" name="CollectfromDate" id="CollectfromDate" placeholder ="From Date" autocomplete="Off" required/>
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="form-control" name="CollecttoDate" id="CollecttoDate" placeholder ="To Date" autocomplete="Off" required/>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-2" style="margin-top:18px;">
                            <input type="button" value="Go" class="btn green-meadow" id="PaymentVsCollections" >
                            <input id="csrf-token" type="hidden" name="_token" value="{{csrf_token()}}">
                    </div>
                    <div class="col-md-2" style="margin-top:10px;">
                        <div class="portlet light tasks-widget">
                            <div class="portlet-body">
                                <div class="tabbable-line">
                                    <ul class="nav nav-tabs">
                                        <div class="actions" style="margin-right:10px; "> 
                                            <a type="button" id="salesexport" class="btn green-meadow pull-right">Export Payments Details</a> 
                                        </div>
                                    <form id="sales_details_export" action="/collections/getexportdetails" method="POST">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="PayfromDate" id="PayfromDate_export" value="">
                                            <input type="hidden" name="PaytoDate" id="PaytoDate_export" value="">
                                            <input type="hidden" name="CollectfromDate" id="CollectfromDate_export" value="">
                                            <input type="hidden" name="CollecttoDate" id="CollecttoDate_export" value="">
                                    </form>
                                        <!-- Removed Invertory Tab UI Button here -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
                    <!-- </form>             -->
            </div>
            
        </div>
 </div>
 <div class="tab-content" >
    <div class="tab-pane active" id="ff_list_tab">
        <div id="ff_list_error" class="hideError" align="center"></div>
        <div id="ff_list_table"><table id="dashboard_list" style="white-space: nowrap;"></table></div>
    </div>             
 </div>


@stop

@section('style')
<!-- Custom Dashboard Style -->
<link href="{{ URL::asset('assets/global/css/dashboard-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
<style type="text/css">
    .textRightAlign {
        text-align:right !important;
    }
</style>
<!-- Custom Dashboard JS -->
<script src="{{ URL::asset('assets/global/scripts/PaymentVsCollections.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.js"></script>

<style type="text/css">
    .ui-iggrid .ui-iggrid-tablebody td {
    padding: 10px;
    font-size: 12px !important;
    font-weight: normal;
}
</style>>
<script>
 $("#salesexport").click(function(){

var csrf_token = $('#csrf-token').val();
var PaytoDate=$("#PaytoDate").val();
var PayfromDate=$("#PayfromDate").val();
var CollectfromDate= $("#CollectfromDate").val();
var CollecttoDate =  $("#CollecttoDate").val();
//setting value for payfromdate
$('#PayfromDate_export').val(PayfromDate);
$('#PaytoDate_export').val(PaytoDate);
$('#CollectfromDate_export').val(CollectfromDate);
$('#CollecttoDate_export').val(CollecttoDate);
$('#sales_details_export').submit();
})
</script>


@stop
@extends('layouts.footer')   