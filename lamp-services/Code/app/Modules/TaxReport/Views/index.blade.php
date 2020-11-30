@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height: auto;">
            <div class="portlet-title">
                <div class="caption">{{trans('taxReportLabels.heading_1')}}</div>
                <input type="hidden" name="filtered_data_export" id="filtered_data_export">
                <div class="tools ">
                    <div class="btn-group">
                        <!-- <a type="button" class="btn green-meadow" data-id="#" data-toggle="modal" data-target="#createrule-modal">Export</a> -->
                        <a href="{{ URL('/taxreport/excelexport') }}" id="taxreport_export" data-id="#" data-toggle="modal" class="btn green-meadow">Export</a>    
                    </div>

                   <!--  <div class="modal fade" id="createrule-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Export</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet box">
                                                <div class="portlet-body">
                                                    {{ Form::open(array('url' => '/taxreport/excelexport', 'id' => 'exportform'))}}
                                                    <div class="row">
                                                        <div class="col-md-6" style="display:none;">
                                                            <div class="form-group">
                                                                <label class="control-label">Export Type:</label>
                                                                <input type="radio" name="exportType" id="exportType" value="excel" checked="checked">Export to Excel
                                                                <input type="radio" name="exportType" id="exportType" value="pdf">Export to PDF
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="hidden" name="type" id="type">
                                                            <div class="form-group">
                                                                <label class="control-label">Choose Period</label>
                                                                <select id="period" name="period" class="form-control">
                                                                  <option value="this_week">This Week</option>
                                                                  <option value="last_week">Last Week</option>
                                                                  <option value="this_month">This Month</option>
                                                                  <option value="last_month">Last Month</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                 
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" class="btn green-meadow" id="export_button">Export Excel</button>
                                                        </div>
                                                    </div>
                                                    {{ Form::close() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <span data-original-title="Filters" data-placement="top" class="tooltips"><a title="Filter" href="javascript:void(0);" id="toggle_filter"><i class="fa fa-filter fa-lg"></i></a><span data-original-title="Export to Excel" data-placement="top" class="tooltips">
                            </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="col-md-4 control-label greenlabel">{{trans('taxReportLabels.outer_filter')}}</label>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="caption-helper sorting">
                                                            <select id="report_type" name="report_type" class="form-control" placeholder="Tax Report Type" >
                                                                <option value="Inward">Inward</option>
                                                                <option value="Outward">Outward</option>
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <form id="taxreport_filters" action="" method="post">   
                                    <div id="filters_div" style="display:none;">
                                        <div id="inward_filter_div">
                                            <div class="row">
                                                <div class="col-md-3">                        
                                                    <div class="form-group">
                                                        <select name="dc_name" id="dc_name" class="form-control multi-select-box" multiple="multiple" placeholder="{{trans('taxReportLabels.filter.dcName')}}">
                                                            @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                                            <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">                        
                                                    <div class="form-group">
                                                        <select name="state_id" id="state_id" class="form-control multi-select-search-box" multiple="multiple" placeholder="{{trans('taxReportLabels.filter.state')}}">
                                                            @foreach ($filter_options['state'] as $state_id => $state_name)
                                                            <option value="{{ $state_id }}">{{ $state_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>                        
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div id="input_transac_no">
                                                            <select name="trans_no" id="trans_no" class="form-control multi-select-search-box" multiple="multiple" placeholder="{{trans('taxReportLabels.filter.trans_num')}}">
                                                                @foreach ($filter_options['transaction_no'] as $transaction_no)
                                                                <option value="{{ $transaction_no }}">{{ $transaction_no }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div id="output_transac_no">
                                                            <select name="trans_no_out" id="trans_no_out" class="form-control multi-select-search-box" multiple="multiple" placeholder="{{trans('taxReportLabels.filter.trans_num')}}">
                                                                @foreach ($filter_options['transaction_no_out'] as $transaction_no_out)
                                                                <option value="{{ $transaction_no_out }}">{{ $transaction_no_out }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">                        
                                                    <div class="form-group">
                                                        <select name="trans_type" id="trans_type" class="form-control multi-select-box" multiple="multiple" placeholder="{{trans('taxReportLabels.filter.trans_type')}}">
                                                            @foreach ($filter_options['transaction_type'] as $transaction_value => $transaction_name)
                                                            <option value="{{ $transaction_value }}">{{ $transaction_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>                        
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">                        
                                                    <div class="form-group">
                                                        <select name="tax_type" id="tax_type" class="form-control multi-select-box" multiple="multiple" placeholder="{{trans('taxReportLabels.filter.tax_type')}}">
                                                            @foreach ($filter_options['tax_type'] as $tax_type)
                                                            <option value="{{ $tax_type }}">{{ $tax_type }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>                        
                                                </div>
                                                <div class="col-md-3">                        
                                                    <div class="form-group">
                                                        <div class="input-icon input-icon-sm right">
                                                            <i class="fa fa-calendar"></i>
                                                            <input type="text" name="transac_date_from" id="transac_date_from" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{trans('taxReportLabels.filter.date_from')}}">
                                                        </div>
                                                    </div>                        
                                                </div>
                                                <div class="col-md-3">                        
                                                    <div class="form-group">
                                                        <div class="input-icon input-icon-sm right">
                                                            <i class="fa fa-calendar"></i>
                                                            <input type="text" name="transac_date_to" id="transac_date_to" class="form-control end_date dp" value="" autocomplete="off" placeholder="{{trans('taxReportLabels.filter.date_to')}}">
                                                        </div>
                                                    </div>                        
                                                </div>
                                                <div class="col-md-3">                        
                                                    <div class="form-group">
                                                        <input type="button" value="{{trans('taxReportLabels.filter.filterBtn')}}" class="btn btn-success" onclick="inputFilterGrid();"> &nbsp;
                                                        <input type="button" value="{{trans('taxReportLabels.filter.resetBtn')}}" class="btn btn-success" onclick="inputReset(); resetInputGrid();">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr />
                                    </div>
                                </form>
                                <div class="table-scrollable">
                                    <table id="inward_grid"></table>
                                    <table id="outward_grid"></table>
                                </div>
                            </div>
                            </div>
                            </div>
                            </div>

                            @stop

                            @section('userscript')
                            <style type="text/css">   
                                .textLeftAlign {
                                    text-align:left;
                                }

                                .textRightAlign {
                                    text-align:right;
                                }

                                ::-webkit-input-placeholder { /* Chrome/Opera/Safari */
                                    color: #928e8e;
                                    font-weight: bold;
                                }
                                ::-moz-placeholder { /* Firefox 19+ */
                                    color: #928e8e;
                                    font-weight: bold;
                                }
                                :-ms-input-placeholder { /* IE 10+ */
                                    color: #928e8e;
                                    font-weight: bold;
                                }
                                :-moz-placeholder { /* Firefox 18- */
                                    color: #928e8e;
                                    font-weight: bold;
                                }

                                .greenlabel{
                                    text-decoration: none !important;
                                    color: #32c5d2 !important;
                                    font-weight: bold!important; font-size:14px;
                                    line-height: 2; margin-bottom: 0; padding-bottom: 0;
                                }

                                #loadingmessage{ z-index: 9999999999 !important; position: relative; top: 50% !important; left: 50% !important;}

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
                            <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
                            <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
                            <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
                            <!--Sumoselect CSS Files-->
                            <link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />

                            <!--Ignite UI Required Combined JavaScript Files--> 
                            <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
                            <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
                            <!-- jquery validation file -->
                            <script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
                            <!-- jquery validatin file included -->
                            <!--Sumoselect JavaScriptFiles-->
                            <script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
                            @extends('layouts.footer')
                            <script type="text/javascript">
                                                            $(document).ready(function () {
                                                                var type = $("#report_type").val();
                                                                $("#type").val(type);
                                                                window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
                                                                window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
                                                                $("#toggle_filter").click(function () {
                                                                    $("#filters_div").toggle("fast", function () {});
                                                                });
                                                                $("#output_transac_no").hide();

                                                                $('#transac_date_from').datepicker({
                                                                    maxDate: 0,
                                                                    onSelect: function () {
                                                                        var select_date = $(this).datepicker('getDate');
                                                                        var nextdayDate = getNextDay(select_date);
                                                                        $('#transac_date_to').datepicker('option', 'minDate', nextdayDate);
                                                                    }
                                                                });

                                                                $('#transac_date_to').datepicker({
                                                                    maxDate: '+0D',
                                                                });
                                                            });

                                                            $("#report_type").change(function () {
                                                                var type = $("#report_type").val();
                                                                $("#type").val(type);
                                                                if (type == "Outward")
                                                                {
                                                                    $('#outward_grid').igGrid({
                                                                        dataSource: "/taxreport/outward",
                                                                        dataSourceType: "json",
                                                                        responseDataKey: "results",
                                                                        primaryKey: "outward_id",
                                                                        height: '400px',
                                                                        columns: [
                                                                            {headerText: "{{trans('taxReportLabels.gridlevel_1_column_1_1')}}", key: "outward_id", dataType: "number", width: "8%", template: '<div class="textRightAlign"> ${outward_id} </div>'},
                                                                            {headerText: "{{trans('taxReportLabels.gridlevel_1_column_2')}}", key: "product_title", dataType: "string", width: "20%"},
                                                                            {headerText: "{{trans('taxReportLabels.gridlevel_1_column_3')}}", key: "transaction_no", dataType: "string", width: "12%"},
                                                                            {headerText: "{{trans('taxReportLabels.gridlevel_1_column_4')}}", key: "master_lookup_name", dataType: "string", width: "12%"},
                                                                            {headerText: "{{trans('taxReportLabels.gridlevel_1_column_5')}}", key: "state", dataType: "string", width: "10%"},
                                                                            {headerText: "{{trans('taxReportLabels.gridlevel_1_column_6')}}", key: "tax_type", dataType: "string", width: "7%"},
                                                                            {headerText: "{{trans('taxReportLabels.gridlevel_1_column_7')}}", key: "tax_percent", dataType: "number", width: "5%", template: '<div class="textRightAlign"> ${tax_percent} </div>'},
                                                                            {headerText: "{{trans('taxReportLabels.gridlevel_1_column_8')}}", key: "tax_amount", dataType: "number", width: "8%", template: '<div class="textRightAlign"> ${tax_amount} </div>'},
                                                                            {headerText: "{{trans('taxReportLabels.gridlevel_1_column_9')}}", key: "lp_wh_name", dataType: "string", width: "18%"}
                                                                        ],
                                                                        features: [
                                                                            // {
                                                                            //     name: 'Paging',
                                                                            //     type: 'remote',
                                                                            //     pageSize: 10,
                                                                            //     recordCountKey: 'TotalRecordsCount',
                                                                            //     pageIndexUrlKey: "page",
                                                                            //     pageSizeUrlKey: "pageSize"
                                                                            // }
                                                                            {
                                                                                recordCountKey: 'TotalRecordsCount',
                                                                                chunkIndexUrlKey: 'page',
                                                                                chunkSizeUrlKey: 'pageSize',
                                                                                chunkSize: 10,
                                                                                name: 'AppendRowsOnDemand',
                                                                                loadTrigger: 'auto',
                                                                                type: 'remote'
                                                                            }
                                                                        ]
                                                                    });
                                                                }
                                                            });

                                                            function getNextDay(select_date) {
                                                                select_date.setDate(select_date.getDate());
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
                                                            $(function () {
                                                                $('#inward_grid').igGrid({
                                                                    dataSource: "/taxreport/inward",
                                                                    dataSourceType: "json",
                                                                    responseDataKey: "results",
                                                                    primaryKey: "inward_id",
                                                                    height: '400px',
                                                                    columns: [
                                                                        {headerText: "{{trans('taxReportLabels.gridlevel_1_column_1')}}", key: "inward_id", dataType: "number", width: "8%", template: '<div class="textRightAlign"> ${inward_id} </div>'},
                                                                        {headerText: "{{trans('taxReportLabels.gridlevel_1_column_2')}}", key: "product_title", dataType: "string", width: "20%"},
                                                                        {headerText: "{{trans('taxReportLabels.gridlevel_1_column_3')}}", key: "transaction_no", dataType: "number", width: "10%", template: '<div class="textRightAlign"> ${transaction_no} </div>'},
                                                                        {headerText: "{{trans('taxReportLabels.gridlevel_1_column_4')}}", key: "master_lookup_name", dataType: "string", width: "12%"},
                                                                        {headerText: "{{trans('taxReportLabels.gridlevel_1_column_5')}}", key: "state", dataType: "string", width: "10%"},
                                                                        {headerText: "{{trans('taxReportLabels.gridlevel_1_column_6')}}", key: "tax_type", dataType: "string", width: "7%"},
                                                                        {headerText: "{{trans('taxReportLabels.gridlevel_1_column_7')}}", key: "tax_percent", dataType: "number", width: "7%", template: '<div class="textRightAlign"> ${tax_percent} </div>'},
                                                                        {headerText: "{{trans('taxReportLabels.gridlevel_1_column_8')}}", key: "tax_amount", dataType: "number", width: "8%", template: '<div class="textRightAlign"> ${tax_amount} </div>'},
                                                                        {headerText: "{{trans('taxReportLabels.gridlevel_1_column_9')}}", key: "lp_wh_name", dataType: "string", width: "18%"}
                                                                    ],
                                                                    features: [
                                                                        // {
                                                                        //     name: 'Paging',
                                                                        //     type: 'remote',
                                                                        //     pageSize: 10,
                                                                        //     recordCountKey: 'TotalRecordsCount',
                                                                        //     pageIndexUrlKey: "page",
                                                                        //     pageSizeUrlKey: "pageSize"
                                                                        // }
                                                                        {
                                                                            recordCountKey: 'TotalRecordsCount',
                                                                            chunkIndexUrlKey: 'page',
                                                                            chunkSizeUrlKey: 'pageSize',
                                                                            chunkSize: 10,
                                                                            name: 'AppendRowsOnDemand',
                                                                            loadTrigger: 'auto',
                                                                            type: 'remote'
                                                                        }


                                                                    ]
                                                                });
                                                            });

//                                                            
                                                            $("#report_type").on("change", function () {
                                                                var tax_report_type = $("#report_type").val(),
                                                                        outURL = "/taxreport/outward",
                                                                        inURL = "/taxreport/inward";
                                                                        $("#type").val(tax_report_type);
                                                                if (tax_report_type !== 'Inward') {
                                                                    $("#inward_grid_container").hide();
                                                                    $("#outward_grid_container").show();
                                                                    $("#input_transac_no").hide();
                                                                    $("#output_transac_no").show();
                                                                    $("#outward_grid").igGrid({
                                                                        dataSource: outURL
                                                                    });
                                                                } else {
                                                                    $("#inward_grid_container").show();
                                                                    $("#outward_grid_container").hide();
                                                                    $("#input_transac_no").show();
                                                                    $("#output_transac_no").hide();
                                                                    $("#inward_grid").igGrid({
                                                                        dataSource: inURL
                                                                    });
                                                                }

                                                            });

                                                            function inputFilterGrid() {
                                                                $("#filters_div").toggle("fast", function () {});

                                                                var inputMain = [], inputFilter = {}, dcnames = {}, state = {}, transac_no = {}, transac_type = {}, taxtype = {}, tax_report_type = $("#report_type").val();

                                                                $("#dc_name option:selected").each(function (i) {
                                                                    if ($(this).length) {
                                                                        dcnames[i] = $(this).val();
                                                                    }
                                                                });
                                                                inputFilter["dc_name"] = dcnames;

                                                                $("#state_id option:selected").each(function (i) {
                                                                    if ($(this).length) {
                                                                        state[i] = $(this).val();
                                                                    }
                                                                });
                                                                inputFilter["state"] = state;

                                                                $("#trans_type option:selected").each(function (i) {
                                                                    if ($(this).length) {
                                                                        transac_type[i] = $(this).val();
                                                                    }
                                                                });
                                                                inputFilter["trans_type"] = transac_type;

                                                                $("#tax_type option:selected").each(function (i) {
                                                                    if ($(this).length) {
                                                                        taxtype[i] = $(this).val();
                                                                    }
                                                                });
                                                                inputFilter["tax_type"] = taxtype;

                                                                inputFilter["transac_from"] = $("#transac_date_from").val();

                                                                inputFilter["transac_to"] = $("#transac_date_to").val();

                                                                if (tax_report_type === 'Inward') {
                                                                    $("#trans_no option:selected").each(function (i) {
                                                                        if ($(this).length) {
                                                                            transac_no[i] = $(this).val();
                                                                        }
                                                                    });
                                                                    inputFilter["trans_number"] = transac_no;

                                                                    inputMain.push(JSON.stringify(inputFilter));

                                                                    var filterURL = "/taxreport/inward?filterData=" + inputMain;
                                                                    $("#taxreport_export").attr("href", "/taxreport/excelexport?type=Inward&exportType=excel&data=" + inputMain);
                                                                    // $("#taxreport_export_pdf").attr("href", "/taxreport/excelexport?type=Inward&exportType=pdf&data=" + inputMain);
                                                                    $("#inward_grid").igGrid({dataSource: filterURL});
                                                                } else {
                                                                    $("#trans_no_out option:selected").each(function (i) {
                                                                        if ($(this).length) {
                                                                            transac_no[i] = $(this).val();
                                                                        }
                                                                    });
                                                                    // console.log(transac_no+"+++testing+++");
                                                                    inputFilter["trans_number"] = transac_no;
                                                                    console.log(inputFilter);
                                                                    inputMain.push(JSON.stringify(inputFilter));

                                                                    var filterURL = "/taxreport/outward?filterData=" + inputMain;
                                                                    $("#taxreport_export").attr("href", "/taxreport/excelexport?type=Outward&exportType=excel&data=" + inputMain);
                                                                    // $("#taxreport_export_pdf").attr("href", "/taxreport/excelexport?type=Outward&exportType=pdf&data=" + inputMain);
                                                                    $("#outward_grid").igGrid({dataSource: filterURL});
                                                                }

                                                                // inputReset();
                                                            }

                                                            function inputReset() {
                                                                 var tax_report_type = $("#report_type").val();
                                                                $("#taxreport_export").attr("href", "/taxreport/excelexport?type="+tax_report_type+"&exportType=excel");
                                                                
                                                                var dc_name = [], state = [], transaction_number = [], transaction_number_out = [], transaction_type = [], tax_type = [];

                                                                $('#dc_name option:selected').each(function () {
                                                                    dc_name.push($(this).index());
                                                                });
                                                                for (var i = 0; i < dc_name.length; i++) {
                                                                    $('.multi-select-box')[0].sumo.unSelectItem(dc_name[i]);
                                                                }

                                                                $('#trans_type option:selected').each(function () {
                                                                    transaction_type.push($(this).index());
                                                                });
                                                                for (var i = 0; i < transaction_type.length; i++) {
                                                                    $('.multi-select-box')[1].sumo.unSelectItem(transaction_type[i]);
                                                                }

                                                                $('#tax_type option:selected').each(function () {
                                                                    tax_type.push($(this).index());
                                                                });
                                                                for (var i = 0; i < tax_type.length; i++) {
                                                                    $('.multi-select-box')[2].sumo.unSelectItem(tax_type[i]);
                                                                }

                                                                $('#state_id option:selected').each(function () {
                                                                    state.push($(this).index());
                                                                });
                                                                for (var i = 0; i < state.length; i++) {
                                                                    $('.multi-select-search-box')[0].sumo.unSelectItem(state[i]);
                                                                }

                                                                $('#trans_no option:selected').each(function () {
                                                                    transaction_number.push($(this).index());
                                                                });
                                                                for (var i = 0; i < transaction_number.length; i++) {
                                                                    $('.multi-select-search-box')[1].sumo.unSelectItem(transaction_number[i]);
                                                                }

                                                                $('#trans_no_out option:selected').each(function () {
                                                                    transaction_number_out.push($(this).index());
                                                                });
                                                                for (var i = 0; i < transaction_number_out.length; i++) {
                                                                    $('.multi-select-search-box')[2].sumo.unSelectItem(transaction_number_out[i]);
                                                                }

                                                                $(".dp").val('');
                                                                $(".dp").datepicker("option", "minDate", null).datepicker("option", "maxDate", null);
                                                            }

                                                            function resetInputGrid() {
                                                                var tax_report_type = $("#report_type").val();
                                                                if (tax_report_type === 'Inward') {
                                                                    var mainURL = "/taxreport/inward";
                                                                    $("#inward_grid").igGrid({
                                                                        dataSource: mainURL
                                                                    });
                                                                } else {
                                                                    var mainURL = "/taxreport/outward";
                                                                    $("#outward_grid").igGrid({
                                                                        dataSource: mainURL
                                                                    });
                                                                }
                                                            }

                                                            $(document).ready(function () {
                                                                var type = $("#report_type").val();
                                                                $("#taxreport_export").attr("href", "/taxreport/excelexport?type=" + type + "&exportType=excel");
                                                                $("#taxreport_export_pdf").attr("href", "/taxreport/excelexport?type=" + type + "&exportType=pdf");

                                                            });

                                                            $("#report_type").on("change", function () {
                                                                var type = $("#report_type").val();
                                                                $("#taxreport_export").attr("href", "/taxreport/excelexport?type=" + type + "&exportType=excel");
                                                                $("#taxreport_export_pdf").attr("href", "/taxreport/excelexport?type=" + type + "&exportType=pdf");
                                                            });
                                                            $("#transac_date_from, #transac_date_to").keydown(function () {
                                                                return false;
                                                            });

                                                            $('#createrule-modal').on('hidden.bs.modal', function (e) {
                                                                var value = "excel";
                                                                // $("input[name=exportType][value=" + value + "]").attr('checked', 'checked');
                                                                $("input[name=exportType][value=" + value + "]").prop('checked', true);
                                                                $("#period").prop('selectedIndex',0);
                                                            });

                                                            $('#exportform').submit(function(e) {
                                                                // Coding
                                                                this.submit(); // use the native submit method of the form element
                                                                $('#createrule-modal').modal('toggle');
                                                                return false;
                                                            });
                            </script>
                            @stop
                            @extends('layouts.footer')