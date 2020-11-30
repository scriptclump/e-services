@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
<input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height: auto;">
            <div class="portlet-title">
                <div class="caption">{{ trans('dmsReportLabels.heading_1') }}</div>
                <input type="hidden" name="filtered_data_export" id="filtered_data_export">
                <div class="actions">
                    <span style="display:none;" id="export_to_excel" data-placement="top" class="tooltips"><a href="javascript:" class="btn green-meadow" id="toggleFilter_export">{{ trans('dmsReportLabels.excel') }}</a></span>
                    @if($emailSetupBtnAccess == '1')
                    <span data-placement="top" class="tooltips"><a href="#" data-id="#" data-toggle="modal" data-target="#dms_report_modal" class="btn green-meadow">E-Mail settings</a></span>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div id="filters">
                    <div class="row">
                        <div class="col-md-3">                        
                            <div class="form-group">
                                <select name="dc_name" id="dc_name" class="form-control dc_reset" placeholder="{{ trans('inventorylabel.filters.dc') }}" align="right">
                                    @foreach ($warehouses as $dc_id => $dc_name)
                                    <option value="{{ $dc_id }}">{{ $dc_name }}</option>
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
                            <div class="form-group">
                                <div class="input-icon input-icon-sm right">
                                    <!-- <i class="fa fa-calendar"></i> -->
                                    <input type="number" min='0' step='1' name="days" id="days" class="form-control end_date dp" value="" autocomplete="off" placeholder="{{ trans('dmsReportLabels.filter_inventory_days') }}">
                                </div>
                            </div>                        
                        </div>
                        <div class="col-md-2 text-right">
                            <input type="button" value="{{ trans('dmsReportLabels.filter_submit') }}" class="btn btn-success" onclick="filterGrid();">
                            <input type="button" value="{{ trans('dmsReportLabels.filter_reset') }}" class="btn btn-success" onclick="resetFilters();">
                        </div>
                    </div>
                </div>
                <div class="table-scrollable">
                    <table id="dmsgrid"></table>
                </div>
            </div>
            <div class="modal modal-scroll fade" id="dms_report_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title text-center" id="myModalLabel">Automatic E-Mail Settings</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet box">
                                        <div class="portlet-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="dc_id"><b>DC Name: </b></label>
                                                        <select name="dc_id" id="dc_id" class="form-control dc_reset">
                                                            <option value="noData">Please select DC</option>
                                                            @foreach ($warehouses as $dc_id => $dc_name)
                                                                @if(isset($emailSetupVals["le_wh_id"]) && $dc_id == $emailSetupVals["le_wh_id"])
                                                                    <option value="{{ $dc_id }}" selected>{{ $dc_name }}</option>
                                                                @else
                                                                    <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="period_days"><b>Period (in days): </b></label>
                                                        @if(isset($emailSetupVals["period_days"]))
                                                            <input type="number" name="period_days" id="period_days" class="form-control" min="1" value="{{ $emailSetupVals["period_days"] }}" placeholder="Period in days"/>
                                                        @else
                                                            <input type="number" name="period_days" id="period_days" class="form-control" min="1" placeholder="Period in days"/>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="inventory_days"><b>Inventory Days: </b></label>
                                                        @if(isset($emailSetupVals["inventory_days"]))
                                                            <input type="number" name="inventory_days" id="inventory_days" class="form-control" min="1" value="{{ $emailSetupVals["inventory_days"] }}" placeholder="Inventory in days"/>
                                                        @else
                                                            <input type="number" name="inventory_days" id="inventory_days" class="form-control" min="1" placeholder="Inventory in days"/>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                        <label for="dms_email_setup_btn">&nbsp;</label>
                                                    <button type="submit" class="btn green-meadow" id="dms_email_setup_btn" onclick="dmsEmailSetup();">Save</button>  
                                                    &nbsp;
                                                    <button type="submit" class="btn green-meadow" id="dms_email_setup_btn_cancel" data-toggle="modal" data-target="#dms_report_modal">Cancel</button>  
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('userscript')
<style type="text/css">
    .ui-iggrid .ui-iggrid-tablebody td {
        border: 1px solid #fff !important;
    }
    .slider-container {
        margin-top:15px !important;
    }
    .bootstrap-switch-handle-on {
        color:#fff !important;
        background: #26C281 !important;
    }
    .bootstrap-switch-handle-off {
        color:#fff !important;
        background: #D91E18 !important;
    }
    .parent_child_0{
        padding-left: 30px !important;
    }
    .parent_child_1{
        padding-left: 45px !important;
    }
    .parent_child_2{
        padding-left: 60px !important;
    }
    .parent_child_3{
        padding-left: 75px !important;
    }
    .parent_child_4{
        padding-left: 90px !important;
    }
    .actionss{padding-left: 22px !important;}
    .sorting a{ list-style-type:none !important;text-decoration:none !important;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#ddd !important;}
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
    .SumoSelect > .optWrapper > .options li label {
        white-space: pre-wrap !important;
        word-wrap: break-word !important;
        width: 250px !important;
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
    label {
        padding-bottom: 0px !important;
    }
    .textLeftAlign {
        text-align:left;
    }
    .textRightAlign {
        text-align:right;
    }
    th.ui-iggrid-header:nth-child(3), th.ui-iggrid-header:nth-child(4), th.ui-iggrid-header:nth-child(5), th.ui-iggrid-header:nth-child(6){
        text-align: left !important;
    }
    table[data-childgrid="true"] th.ui-iggrid-header{
        text-align: left !important;
    }
    .inv-title th{
        text-align: center; background-color: #F2F2F2; height: 40px;
    }
    /*edit range slider styles*/	
    .range-min-css{
        -moz-appearance: none;
        border-style: solid;
        border-width: 1px;
        box-sizing: content-box;
        display: block;
        float: left;
        font-size: 14px;
        font-weight: 700;
        height: 20px;
        line-height: 20px;
        margin: 0;
        outline: 0 none;
        padding: 4px;
        text-align: center;
        vertical-align: text-bottom;
        width: 55px;
    }
    .range-max-css{
        -moz-appearance: none;
        border-style: solid;
        border-width: 1px;
        box-sizing: content-box;
        float: right;
        font-size: 14px;
        font-weight: 700;
        height: 20px;
        line-height: 20px;
        margin: 0;
        outline: 0 none;
        padding: 4px;
        position: relative;
        text-align: center;
        top: -30px;
        vertical-align: text-bottom;
        width: 55px;
    }
    .slider{
        padding-top:6px;
        height: 30px;
        margin: 0 80px;
        overflow: visible;
        position: relative;
    }
    #inventorygrid_9_inventory_child_updating_dialog_container {
        position: absolute !important;
        top:-20px !important;
    }
</style>
<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Bootstrap dataepicker CSS Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript"></script>
<!--Nouislider picker CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/nouislider-new/nouislider.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<!--Nouislider picker JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/nouislider-new/nouislider.js') }}" type="text/javascript"></script>
<!--Sumoselect JavaScriptFiles-->
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<!--Ignite UI Required Combined JavaScript Files--> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<!--Bootstrap dataepicker JavaScript Files-->
<!-- <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script> -->
<!-- <script src="{{ URL::asset('assets/admin/pages/scripts/MasterSkuListReport/masterSkuListReport.js') }}" type="text/javascript"></script> -->
@extends('layouts.footer')
<script type="text/javascript">
    $(document).ready(function () {
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

    function filterGrid() {
        var dcname = $("#dc_name").val();
        var fromdate = $("#transac_date_from").val();
        var todate = $("#transac_date_to").val();
        var numberofdays = $("#days").val();
        var arr = {}, mainfilters = [];
        if (dcname === 'NULL' || dcname === '' || dcname === null)
        {
            alert("Please select a DC name");
            $("#dc_name").focus();
            return false;
        }
        if (fromdate == "")
        {
            alert("start date couldn't blank");
            $("#transac_date_from").focus();
            return false;
        }
        if (todate == "")
        {
            alert("End date couldn't blank");
            $("#transac_date_to").focus();
            return false;
        }
        if (numberofdays == "")
        {
            alert("Inventory Days couldn't blank");
            $("#days").focus();
            return false;
        }
        arr['dc_name'] = dcname;
        arr['from_date'] = fromdate;
        arr['todate'] = todate;
        arr['num_days'] = numberofdays;
        mainfilters.push(JSON.stringify(arr));
        $("#toggleFilter_export").attr("href", "getexport?filtersData=" + mainfilters);

        $("#dmsgrid").igGrid({
            dataSource: '/meanproducts/griddata?filtersData=' + mainfilters,
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: "Product ID", key: "Product ID", dataType: "number", width: "80px", template: '<div class="textRightAlign"> ${Product ID} </div>'},
                {headerText: "Manufacturer", key: "Manufacturer", dataType: "string", width: "200px"},
                {headerText: "Product Title", key: "Product Title", dataType: "string", width: "200px"},
                {headerText: "SKU", key: "SKU", dataType: "string", width: "120px"},
                                            {headerText: "Product Group Id", key: "Product Group Id", dataType: "string", width: "150px"},
                {headerText: "CFC Qty", key: "CFC Qty", dataType: "number", width: "100px", template: '<div class="textRightAlign"> ${CFC Qty} </div>'},
                {headerText: "Latest MRP", key: "Latest MRP", dataType: "number", width: "100px", template: '<div class="textRightAlign"> ${Latest MRP} </div>'},
                {headerText: "CP Enabled", key: "CP Enabled", dataType: "string", width: "80px"},
                {headerText: "PO Code", key: "PO Code", dataType: "string", width: "130px"},
                {headerText: "PO Date", key: "PO Date", dataType: "date", width: "100px", format: "date", template: '<div class="textRightAlign"> ${PO Date} </div>'},
                {headerText: "PO Qty", key: "PO Qty", dataType: "number", width: "100px", template: '<div class="textRightAlign"> ${PO Qty} </div>'},
                {headerText: "AVG Day Sales", key: "AVG Day Sales", dataType: "number", width: "100px", template: '<div class="textRightAlign"> ${AVG Day Sales} </div>'},
                {headerText: "Available CFC", key: "Available CFC", dataType: "number", width: "130px", template: '<div class="textRightAlign"> ${Available CFC} </div>'},
                                            {headerText: "Returned Pending Qty (CFC)", key: "Return Pending Qty", dataType: "number", width: "190px", template: '<div class="textRightAlign"> ${Return Pending Qty} </div>'},
                                            {headerText: "Open to Buy (CFC)", key: "Open to buy CFC", dataType: "number", width: "130px", template: '<div class="textRightAlign"> ${Open to buy CFC} </div>'},
                {headerText: "Min CFC Rate", key: "Min CFC Rate", dataType: "number", width: "130px", template: '<div class="textRightAlign"> ${Min CFC Rate} </div>'},
                {headerText: "Last Bought CFC Rate", key: "Last Bought CFC Rate", dataType: "number", width: "130px", template: '<div class="textRightAlign"> ${Last Bought CFC Rate} </div>'},
                {headerText: "Supplier", key: "Supplier", dataType: "string", width: "200px"},
                {headerText: "WD/SWD", key: "WD-SWD", dataType: "string", width: "170px"},
            ],
            features: [
                // {
                //     name: "Sorting",
                //     type: "remote",
                //     columnSettings: [
                //         {columnKey: 'actions', allowSorting: false},
                //         // {columnKey: 'TotalQuantity', allowSorting: false},
                //         // {columnKey: "PrimaryKEY", allowSorting: true, currentSortDirection: "descending"}

                //     ]
                // },


                // {
                //     name: 'Paging',
                //     type: 'remote',
                //     pageSize: 10,
                //     recordCountKey: 'TotalRecordsCount',
                //     pageIndexUrlKey: "page",
                //     pageSizeUrlKey: "pageSize"
                // }
                {
                    name: "Filtering",
                    type: "local",
                    columnSettings: [
                        {columnKey: 'po_date', allowFiltering: false},
                        {columnKey: 'cfc_qty', allowFiltering: false},
                        {columnKey: 'cp_enabled', allowFiltering: false},
                        {columnKey: 'po_date', allowFiltering: false},
                        {columnKey: 'po_date', allowFiltering: false},
                        {columnKey: 'po_date', allowFiltering: false},
                    ]
                },
                {
                    name: 'Paging',
                    type: "local",
                    pageSize: 10
                }

            ],
            primaryKey: 'product_id',
            height: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            rendered: function (evt, ui) {
                $("#dmsgrid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
                $("#dmsgrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();    
                $("#dmsgrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();    
                $("#dmsgrid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
            }
        });
        $("#export_to_excel").show();
    }

    function resetFilters() {
        $("#transac_date_from").val("");
        $("#transac_date_to").val("");
        $("#days").val("");
        $("#export_to_excel").css("display", "none");
        $("#dmsgrid").igGrid('destroy');
        var dc_name = [];
        $('#dc_name option:selected').each(function () {
            dc_name.push($(this).index());
        });
        $(".dc_reset").val($(".dc_reset option:first").val());
    }

    $("#transac_date_from, #transac_date_to").keydown(function () {
        return false;
    });
    
    function dmsEmailSetup() {
        var token = $("#token_value").val(),
            dmsEmailVal = {};
        dmsEmailVal["dc_id"] = $("#dc_id").val();
        dmsEmailVal["period_days"] = $("#period_days").val();
        dmsEmailVal["inventory_days"] = $("#inventory_days").val();
        if (dmsEmailVal["dc_id"] == 'noData')
        {
            alert("Please select DC name");
            $("#dc_id").focus();
            return false;
        }
        if (dmsEmailVal["period_days"] == "")
        {
            alert("Period couldn't blank");
            $("#period_days").focus();
            return false;
        } else if(dmsEmailVal["period_days"] == 0){
            alert("Period couldn't be zero");
            $("#period_days").focus();
            return false;
        }
        if (dmsEmailVal["inventory_days"] == "")
        {
            alert("Inventory Days couldn't blank");
            $("#inventory_days").focus();
            return false;
        } else if(dmsEmailVal["inventory_days"] == 0){
            alert("Inventory couldn't be zero");
            $("#inventory_days").focus();
            return false;
        }
        if (confirm("Are you sure in updating the automatic email settings ?")) {
            $.ajax({
                type: "POST",
                url: "/meanproducts/dmsemailsetup?_token=" + token,
                data: dmsEmailVal,
                success: function (data) {
                    $('#dms_report_modal').modal('toggle');
                    if(data.call_message === "success"){
                        $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Auto email setup was done successfully!</div></div>');
                        $(".alert-success").fadeOut(20000);
                    } else {
                        $("#error_message").html('<div class="flash-message"><div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert"></button>There was a problem in setting up the auto email!</div></div>');
                        $(".alert-warning").fadeOut(20000);
                    }
                    $("#dc_id").val(data.dc_id);
                    $("#period_days").val(data.period_days);
                    $("#inventory_days").val(data.inventory_days);
                }
            });
        }
    }
</script>

@stop
@extends('layouts.footer')