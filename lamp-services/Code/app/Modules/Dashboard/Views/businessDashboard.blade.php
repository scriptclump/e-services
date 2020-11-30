@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<ul class="page-breadcrumb breadcrumb">
    <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i> </li>
    <li><span class="bread-color">Business Dashboard</span><i class="fa fa-angle-right" aria-hidden="true"></i></li>
</ul>
<div class="row">
    <div class="col-md-12" style="padding-right:5px;">
    @foreach ($htmlData as $eachData)
    <div id="{{ $eachData['dashboard_master_id'] }}_dashboard_div">
        <div class="col-md-6" style="padding:0px 10px 10px 0px ">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase font-dark">{{ $eachData['dashboard_name'] }}</span>
                    </div>
                    <div class="col-md-9 pull-right" style="padding-right:0px;">
                        <div class="col-md-5 pad">
                            <select class="status_select_mul" multiple="multiple" id="{{ $eachData['dashboard_master_id'] }}_filter_status" name="{{ $eachData['dashboard_master_id'] }}_filter_status">
                                @foreach ($eachData['filter_by'] as $filterBy)
                                <option value="{{ $filterBy }}">{{ $filterBy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5 pad">
                            <select class="period_select" id="{{ $eachData['dashboard_master_id'] }}_filter_period" name="{{ $eachData['dashboard_master_id'] }}_filter_period" onchange="displayCall('{{ $eachData['dashboard_master_id'] }}')">
                                <option></option>
                                @foreach ($eachData['period_by'] as $key => $value)
                                <option value="{{ $value }}">{{ $key }}</option>
                                @endforeach
                                <option value="date and picker">
                                    Custom Date
                                </option>
                            </select>
                        </div>
                        <div class="col-md-1 pad">
                        <a class="btn btn-primary" title="Redraw Graph" id="{{ $eachData['dashboard_master_id'] }}_draw_button" onclick="filterCall('{{ $eachData['dashboard_master_id'] }}')">
                            <i class="fa fa-paint-brush" aria-hidden="true"></i>
                        </a>
                        </div>
                        <div class="col-md-1 pad">
                            <a class="btn btn-primary" title="Reset Filter" id="{{ $eachData['dashboard_master_id'] }}_reset_button" onclick="resetCall('{{ $eachData['dashboard_master_id'] }}')">
                                <i class="fa fa-undo"></i>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 pull-right" id="{{ $eachData['dashboard_master_id'] }}_date_picker" style="display: none;">                           
                            <div class="input-group col-md-5 datepicker_float">
                                <input type="text" class="form-control dp end_date" placeholder="To Date" id="{{ $eachData['dashboard_master_id'] }}_to_datepick" name="{{ $eachData['dashboard_master_id'] }}_to_datepick" />
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="input-group col-md-5 datepicker_float">
                                <input type="text" class="form-control dp start_date" placeholder="From Date" id="{{ $eachData['dashboard_master_id'] }}_from_datepick" name="{{ $eachData['dashboard_master_id'] }}_from_datepick" />
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="{{ $eachData['dashboard_master_id'] }}_graph_div" class="graph-outer"></div>
                    <div id="{{ $eachData['dashboard_master_id'] }}_loader_div" class="loader-outer">
                        <div class="loader" style=""></div>
                    </div>
                    <input id="take" type="hidden" name="_token" value="{{csrf_token()}}">
                </div>
                <div class="portlet-title"></div>
                <div class="portlet-body">
                    <a class="fa fa-bar-chart fa-lg" id="{{ $eachData['dashboard_master_id'] }}_bar" aria-hidden="true" title="Bar Chart"></a> &nbsp;
                    <a class="fa fa-area-chart fa-lg" id="{{ $eachData['dashboard_master_id'] }}_area" aria-hidden="true" title="Area Chart"></a> &nbsp;
                    <a class="fa fa-pie-chart fa-lg" id="{{ $eachData['dashboard_master_id'] }}_pie" aria-hidden="true" title="Pie Chart"></a> &nbsp;
                    <a class="fa fa-line-chart fa-lg" id="{{ $eachData['dashboard_master_id'] }}_line" aria-hidden="true" title="Line Chart"></a> &nbsp;
                    <a class="fa fa-th fa-lg" id="{{ $eachData['dashboard_master_id'] }}_table" aria-hidden="true" title="Grid View"></a>
                    
                    <input type="hidden" value="{{ $eachData['dashboard_name'] }}" id="{{ $eachData['dashboard_master_id'] }}_dashboard_name"/>
                    <input type="hidden" value="{{ $eachData['proc_name'] }}" id="{{ $eachData['dashboard_master_id'] }}_proc_name"/>
                    <input type="hidden" value="{{ $eachData['x-axis_name'] }}" id="{{ $eachData['dashboard_master_id'] }}_xaxis"/>
                    <input type="hidden" value="{{ $eachData['y-axis_name'] }}" id="{{ $eachData['dashboard_master_id'] }}_yaxis"/>
                    <input type="hidden" value="{{ $eachData['chart_type'] }}" id="{{ $eachData['dashboard_master_id'] }}_constant_chart_type"/>
                    <input type="hidden" value="{{ $eachData['chart_type'] }}" id="{{ $eachData['dashboard_master_id'] }}_chart_type"/>
                    <input type="hidden" value="{{ $eachData['period'] }}" id="{{ $eachData['dashboard_master_id'] }}_period"/>
                    <input type="hidden" value="{{ $eachData['proc_name'] }}" id="{{ $eachData['dashboard_master_id'] }}_pproc_name"/>
                    <input type="hidden" value="{{ $eachData['dt_from'] }}" id="{{ $eachData['dashboard_master_id'] }}_dt_from"/>
                    <input type="hidden" value="{{ $eachData['dt_to'] }}" id="{{ $eachData['dashboard_master_id'] }}_dt_to"/>
                    <input type="hidden" value="{{ $eachData['status_list'] }}" id="{{ $eachData['dashboard_master_id'] }}_status_list"/>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
</div>
@stop

@section('style')
<style type="text/css">
    .page-content { background:none !important; }
    .tableChart{ width: 10%; color: black; font-size: 12px; }
    .fa-bar-chart{ color:#3598DC !important;}
    .fa-area-chart{ color:#D91E18 !important;}
    .fa-line-chart{ color:#2AB4C0 !important;}
    .fa-pie-chart{ color:#F3C200 !important;}
    .fa-th{ color:#8E44AD !important;}
    .btn-default {
        border-width: 1px;
        font-size: 14px;
        color:#999 !important;
        border-color: #e5e5e5;
        padding: 4px 10px;
    }
    .btn + .btn {
        margin-left:0px; 
    }
    .graph-outer{
        width: 100%; 
        height: 500px;
        overflow-x: auto;
        overflow-y: hidden;
    }
    .pad{padding:0px 0px 0px 5px; margin:10px 0px; }
    .col-md-9 .col-md-1 .btn{padding: 7px 10px !important;}
    .datepicker_float{float:right !important; margin-left:5px;}
   .loader-outer{    
       background-color: #fff;
        opacity: 0.9;
        width: 96%;
        height: 500px;
        position: absolute;
        top: 5em;
    }
    .loader {
        margin:1em auto;
        font-size: 10px;
        width: 1em;
        height: 1em;
        border-radius: 50%;
        position: absolute;
        text-indent: -9999em;
        -webkit-animation: load5 1.1s infinite ease;
        animation: load5 1.1s infinite ease;
        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
        z-index:999;
        top:22em;
        left:30em;
    }
    @-webkit-keyframes load5 {
        0%,
        100% {
          box-shadow: 0em -2.6em 0em 0em #8fa4ed, 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.5), -1.8em -1.8em 0 0em rgba(143,164,237, 0.7);
        }
        12.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.7), 1.8em -1.8em 0 0em #8fa4ed, 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.5);
        }
        25% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.5), 1.8em -1.8em 0 0em rgba(143,164,237, 0.7), 2.5em 0em 0 0em #8fa4ed, 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        37.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.5), 2.5em 0em 0 0em rgba(143,164,237, 0.7), 1.75em 1.75em 0 0em #8fa4ed, 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        50% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.5), 1.75em 1.75em 0 0em rgba(143,164,237, 0.7), 0em 2.5em 0 0em #8fa4ed, -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        62.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.5), 0em 2.5em 0 0em rgba(143,164,237, 0.7), -1.8em 1.8em 0 0em #8fa4ed, -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        75% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.5), -1.8em 1.8em 0 0em rgba(143,164,237, 0.7), -2.6em 0em 0 0em #8fa4ed, -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        87.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.5), -2.6em 0em 0 0em rgba(143,164,237, 0.7), -1.8em -1.8em 0 0em #8fa4ed;
        }
    }
    @keyframes load5 {
        0%,
        100% {
          box-shadow: 0em -2.6em 0em 0em #8fa4ed, 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.5), -1.8em -1.8em 0 0em rgba(143,164,237, 0.7);
        }
        12.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.7), 1.8em -1.8em 0 0em #8fa4ed, 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.5);
        }
        25% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.5), 1.8em -1.8em 0 0em rgba(143,164,237, 0.7), 2.5em 0em 0 0em #8fa4ed, 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        37.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.5), 2.5em 0em 0 0em rgba(143,164,237, 0.7), 1.75em 1.75em 0 0em #8fa4ed, 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        50% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.5), 1.75em 1.75em 0 0em rgba(143,164,237, 0.7), 0em 2.5em 0 0em #8fa4ed, -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        62.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.5), 0em 2.5em 0 0em rgba(143,164,237, 0.7), -1.8em 1.8em 0 0em #8fa4ed, -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        75% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.5), -1.8em 1.8em 0 0em rgba(143,164,237, 0.7), -2.6em 0em 0 0em #8fa4ed, -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        87.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.5), -2.6em 0em 0 0em rgba(143,164,237, 0.7), -1.8em -1.8em 0 0em #8fa4ed;
        }
    }
</style>
<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<!--Datepicker CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<!--<link href="{{ URL::asset('assets/global/plugins/fullcalendar/lib/cupertino/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />-->
@stop

@section('userscript')
<!--Google Charts JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/google-graphs/loader.js') }}" type="text/javascript"></script>
<!--Sumoselect JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        google.charts.load('visualization', '1', {'packages':['corechart', 'table', 'controls']});
        var gr_data = {!!html_entity_decode($graphData)!!};
        $.each(gr_data, function (key, value) {
            renderDiv(value);
            
            $( function() {
                var dateFormat = 'yy-mm-dd',
                    from = $( "#" + value.dashboard_master_id + "_from_datepick" )
                        .datepicker({
                            dateFormat: 'yy-mm-dd'
                        })
                        .on( "change", function() {
                            to.datepicker( "option", "minDate", getDate( this ) );
                        }),
                    to = $( "#" + value.dashboard_master_id + "_to_datepick" ).datepicker({
                            dateFormat: 'yy-mm-dd'
                        })
                        .on( "change", function() {
                            from.datepicker( "option", "maxDate", getDate( this ) );
                        });

                function getDate( element ) {
                    var date;
                    try {
                        date = $.datepicker.parseDate( dateFormat, element.value );
                    } catch( error ) {
                        date = null;
                    }
                    return date;
                }
            });
        });
        window.MultiSearch = $('.status_select_mul').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search status', placeholder: 'Select status'});
        window.Search = $('.period_select').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search period', placeholder: 'Select period'});
    });
    
    function filterCall(divId){
        var filPeriod = $("#" + divId + "_filter_period").val().split(" and "),
            filStatus = {},
            procedureHidden = $("#" + divId + "_proc_name").val(),
            statLstHidden = $("#" + divId + "_status_list").val(),
            chartTypHidden = $("#" + divId + "_chart_type").val(),
            fromDateHidden = $("#" + divId + "_dt_from").val(),
            toDdateHidden = $("#" + divId + "_dt_to").val();
            $("#" + divId + "_filter_status option:selected").each(function (i) {
                if ($(this).length) {
                    filStatus[i] = $(this).val();
                }
            });

        if(!filPeriod[0]){
            var fromDt = fromDateHidden,
                toDt = toDdateHidden;
        } else if(filPeriod[0] === "date"){
            var fromDt = $("#" + divId + "_from_datepick").val(),
                toDt = $("#" + divId + "_to_datepick").val();
            $("#" + divId + "_date_picker").css("display", "none");
            $("#" + divId + "_from_datepick").val("");
            $("#" + divId + "_to_datepick").val("");
        } else {
            var fromDt = filPeriod[0],
                toDt = filPeriod[1];
        }

        if($.isEmptyObject(filStatus)){
            var status = statLstHidden;
        } else {
            var status = filStatus;
        }

        renderGraph(procedureHidden, fromDt, toDt, status, chartTypHidden, divId);
    }
    
    function resetCall(divId){
        var resStatus = [], resPeriod = [];
        $("#" + divId + "_filter_status option:selected").each(function () {
            resStatus.push($(this).index());
        });
        for (var i = 0; i < resStatus.length; i++) {
            $("#" + divId + "_filter_status")[0].sumo.unSelectItem(resStatus[i]);
        }

        $("#" + divId + "_filter_period option:selected").each(function () {
            resPeriod.push($(this).index());
        });
        for (var i = 0; i < resPeriod.length; i++) {
            $("#" + divId + "_filter_period")[0].sumo.unSelectItem(resPeriod[i]);
        }
        
        $("#" + divId + "_chart_type").val($("#" + divId + "_constant_chart_type").val());
        
        filterCall(divId);
    }

    function renderDiv(divData) {
        renderGraph(divData.proc_name, divData.dt_from, divData.dt_to, divData.status_list, divData.chart_type, divData.dashboard_master_id);
    }

    function renderGraph(procedure, fDate, tDate, sList, chart_type, divId) {
        var token = $("input[name=_token]").val();
        $.ajax({
            type: "POST",
            url: "/graphaction?_token=" + token,
            data: {procedure: procedure, fDate: fDate, tDate: tDate, sList: sList, chart_type: chart_type},
            beforeSend: function () {
                $("#" + divId + "_loader_div").show();
            },
            success: function (final_data)
            {
                $("#" + divId + "_loader_div").hide();
                google.charts.setOnLoadCallback(drawChart(divId, final_data, chart_type));
            }
        });
    }

    function drawChart(divId, final_data, chart_type) {
        var data = new google.visualization.arrayToDataTable(final_data.All),
        pieData = new google.visualization.arrayToDataTable(final_data.Pie),
        chart = new google.visualization.ChartWrapper({
            containerId: divId + '_graph_div'
        }),
        barsButton = document.getElementById(divId + '_bar'), areaButton = document.getElementById(divId + '_area'),
        lineButton = document.getElementById(divId + '_line'), pieButton = document.getElementById(divId + '_pie'),
        tableButton = document.getElementById(divId + '_table');

        function drawColumn() {
            chart.setOptions({isStacked: true, legend: { position: 'bottom' }});
            chart.setChartType('ColumnChart');
            chart.setDataTable(data);
            chart.draw();
        }

        function drawArea() {
            chart.setOptions({pointSize: 3, legend: { position: 'bottom' }});
            chart.setChartType('AreaChart');
            chart.setDataTable(data);
            chart.draw();
        }

        function drawLine() {
            chart.setOptions({pointSize: 3, legend: { position: 'bottom' }});
            chart.setChartType('LineChart');
            chart.setDataTable(data);
            chart.draw();
        }

        function drawPie() {
            chart.setOptions({is3D: true, legend: { position: 'bottom' }});
            chart.setChartType('PieChart');
            chart.setDataTable(pieData);
            chart.draw();
        }

        function drawTable() {
            chart.setChartType('Table');
            chart.setDataTable(data);
            chart.setOptions({width: '100%', height: '100%', alternatingRowStyle: true, showRowNumber: false, frozenColumns: 1,
                page: 'enable', cssClassNames: {headerCell: 'tableChart', tableCell: 'tableChart'}});
            chart.draw();
        }

        barsButton.onclick = function () {
            drawColumn();
            $("#" + divId + "_chart_type").val('Column');
        }

        areaButton.onclick = function () {
            drawArea();
            $("#" + divId + "_chart_type").val('Area');
        }

        lineButton.onclick = function () {
            drawLine();
            $("#" + divId + "_chart_type").val('Line');
        }

        pieButton.onclick = function () {
            drawPie();
            $("#" + divId + "_chart_type").val('Pie');
        }

        tableButton.onclick = function () {
            drawTable();
            $("#" + divId + "_chart_type").val('Table');
        }

        if (chart_type === 'Column') {
            drawColumn();
        } else if (chart_type === 'Area') {
            drawArea();
        } else if (chart_type === 'Line') {
            drawLine();
        } else if (chart_type === 'Pie') {
            drawPie();
        } else if (chart_type === 'Table') {
            drawTable();
        }
    }
    
    function displayCall(divId){
        var selectValue = $("#" + divId + "_filter_period").val().split(" and ");
        console.log(selectValue);
        if(selectValue[0] === "date"){
            $("#" + divId + "_date_picker").css("display", "block");
        } else {
            $("#" + divId + "_date_picker").css("display", "none");
            $("#" + divId + "_from_datepick").val("");
            $("#" + divId + "_to_datepick").val("");
        }
    }
</script> 
@stop
@extends('layouts.footer')