@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
@if(Session::has('flash_message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            {!!Session::get('flash_message')!!}
        </div>
@endif
<input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height: auto;">
            <div class="portlet-title">
                <div class="caption">Picker Efficiency Report</div>
                <input type="hidden" name="filtered_data_export" id="filtered_data_export">
                <div class="actions">
                    <a href="{{ URL('/pickereffcreport/exportgrid') }}" id="del_exe_per_btn_excel" data-id="#" data-toggle="modal" class="btn green-meadow" style="display:none;">Export to Excel</a>    
                    <a href="#" data-id="#" data-toggle="modal" data-target="#Picker-Verification-summary-report" class="btn green-meadow">Picker Summary Report </a>    
                </div>
                
            </div>
            <div class="portlet-body">
                <form id="inventory_filters" action="" method="post">	
                    <div id="filters">
                        <div class="row">
                            <div class="col-md-2">                        
                                <div class="form-group">
                                <div class="input-icon input-icon-sm right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" name="transac_date_from" id="transac_date_from" class="form-control start_date dp" value="" autocomplete="off" placeholder="From Date">
                                </div>
                            </div>
                            </div>

                            <div class="col-md-2">                        
                                <div class="form-group">
                                <div class="input-icon input-icon-sm right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" name="transac_date_to" id="transac_date_to" class="form-control start_date dp" value="" autocomplete="off" placeholder="To Date">
                                </div>
                            </div>
                            </div>

                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="del_executive" id="del_executive" class="form-control multi-select-search-box manf_name_reset" multiple="multiple" placeholder="Picker">
                                      @foreach ($users as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach  
                                    </select>
                                </div>                        
                            </div>

                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="hubs" id="hubs" class="form-control multi-select-search-box manf_name_reset" multiple="multiple" placeholder="Hubs">
                                      @foreach ($allhubs as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach  
                                    </select>
                                </div>                        
                            </div>
                            <div class="col-md-2">                        
                                <div class="form-group">
                                    <button type="button"  class="btn green-meadow" id="search_button" name="search_button">Submit</button>
                                </div>                        
                            </div>

                           

                    </div>
                </form>

                

              




                <div class="table-scrollable">
                    <table class="inv-title">
                        <tr>
                            <th>Picker Efficiency Report</th>
                        </tr>
                        <tr>
                            <td>
                                <table id="del_exe_per_rep_grid"></table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>


                <div class="modal modal-scroll fade" id="Picker-Verification-summary-report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel"> Picker Summary Report</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                {{ Form::open(array('url' => '/pickereffcreport/summaryreport', 'id' => 'orderverification_summary_report'))}}


                                            </div>
                                            <div id='filters'>
                                                <div class="row">

                                                    <div class="col-md-12">                        
                                                        <div class="form-group">
                                                            <div class="input-icon input-icon-sm right">
                                                                <i class="fa fa-calendar"></i>
                                                                <input type="text" name="transac_date_from1" id="transac_date_from1" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('orderverificationlang.popup_from_date') }} " required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">                        
                                                        <div class="form-group">
                                                            <div class="input-icon input-icon-sm right">
                                                                <i class="fa fa-calendar"></i>
                                                                <input type="text" name="transac_date_to1" id="transac_date_to1" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('orderverificationlang.popup_to_date') }}" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                      <button type="submit" class="btn green-meadow btn-block" id="download-excel">Download Picker Summary Report</button>  
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
            </div>

        </div>
    </div>
</div>

@stop

@section('userscript')
<style type="text/css">
.label1 {
    display: -webkit-inline-box !important; margin-top: 30px;
   }
    
    .slider-container{margin-top:15px !important;}
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

/*    th.ui-iggrid-header:nth-child(3), th.ui-iggrid-header:nth-child(4), th.ui-iggrid-header:nth-child(5), th.ui-iggrid-header:nth-child(6){
        text-align: right !important;
    }*/

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

    .SumoSelect .select-all {

    height: 37px !important;
}
	
	#inventorygrid_9_inventory_child_updating_dialog_container{position: absolute !important; top:-20px !important;}

    th.ui-iggrid-header:nth-child(6),th.ui-iggrid-header:nth-child(7), th.ui-iggrid-header:nth-child(8),th.ui-iggrid-header:nth-child(9), th.ui-iggrid-header:nth-child(10), th.ui-iggrid-header:nth-child(16){text-align:right!important}
	
</style>
<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
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

@extends('layouts.footer')
<script type="text/javascript">
    $(document).ready(function () {
        window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..', selectAll: true});
        window.groups_eg_g = $('.groups_eg_g').SumoSelect({search: true});
        
        
        $("#toggleFilter").click(function () {
            $("#filters").toggle("fast", function () {});
        });

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


        $('#transac_date_from1').datepicker({
                                        maxDate: 0,
                                        onSelect: function () {
                                            var select_date = $(this).datepicker('getDate');
                                            var nextdayDate = getNextDay(select_date);
                                            $('#transac_date_to1').datepicker('option', 'minDate', nextdayDate);
                                        }
                                    });

        $('#transac_date_to1').datepicker({
            maxDate: '+0D',
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




    });
    

    

$("#search_button").click(function(){

	var filters = {}, mainfileters = [];
    if($("#transac_date_from").val() == "" || $("#transac_date_to").val() == "")
    {
        alert("Please select date range");
        return false;
    }else
    {
        $("#del_exe_per_btn_excel").show();
    }
	filters['date']            = $("#transac_date_from").val();
    filters['to_date']         = $("#transac_date_to").val();
	filters['del_users']       = $("#del_executive").val();
    filters['hubs']            = $("#hubs").val();

	mainfileters.push(JSON.stringify(filters));
	$("#del_exe_per_btn_excel").attr("href", "/pickereffcreport/exportgrid?filters=" + mainfileters);
    
        if($.trim($("#del_exe_per_rep_grid").html())!='') {
        $("#del_exe_per_rep_grid").data("igGridPaging").options.currentPageIndex = 0;
    }
           $("#del_exe_per_rep_grid").igGrid({
            dataSource: '/pickereffcreport/griddata?filters=' + mainfileters,
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                        {headerText: "Picked Date", key: "picked_date", dataType: "date", width: "80px"},
                        {headerText: "Picked By", key: "picked_By", dataType: "string", width: "100px"},
                        {headerText: "Assgined Date & Time", key: "scheduled_piceker_date", dataType: "string", width: "170px"},
                        
                        {headerText: "Hub Name", key: "hub_name", dataType: "string", width: "100px"},
                        {headerText: "Order No", key: "order_num", dataType: "string", width: "120px", template: '<div class="textLeftAlign"> ${order_num} </div>'},
                        {headerText: "Order Qty", key: "order_qty", dataType: "string",width: "70px", template: '<div class="textRightAlign"> ${order_qty} </div>'},
                        {headerText: "Order Val", key: "order_val", dataType: "number", width: "100px", template: '<div class="textRightAlign"> ${order_val} </div>'},

                        {headerText: "SKUs Order", key: "skus_order", dataType: "string", width: "80px", template: '<div class="textRightAlign"> ${skus_order} </div>'},
                        {headerText: "Picked Qty", key: "picked_qty", dataType: "string",width: "80px",  template: '<div class="textRightAlign"> ${picked_qty} </div>'},
                        {headerText: "Cancelled Qty", key: "cancelled_qty", dataType: "number", width: "100px", template: '<div class="textRightAlign"> ${cancelled_qty} </div>'},

                        {headerText: "Cancellation Reason", key: "comment", dataType: "string", width: "180px", template: '<div class="textLeftAlign"> ${comment} </div>'},
                        {headerText: "Start Time", key: "Picking_Start_Time", dataType: "string", width: "160px", template: '<div class="textLeftAlign"> ${Picking_Start_Time} </div>'},
                        {headerText: "Completion Time", key: "complition_Time", dataType: "string", width: "160px", template: '<div class="textLeftAlign"> ${complition_Time} </div>'},
                        
                        {headerText: "Duration", key: "duration", dataType: "string",width: "100px", template: '<div class="textLeftAlign"> ${duration} </div>'},
                        {headerText: "Area", key: "area_name", dataType: "string", width: "100px", template: '<div class="textLeftAlign"> ${area_name} </div>'},
						{headerText: "Order Fill Rate", key: "order_fill_rate", dataType: "string", width: "100px", template: '<div class="textRightAlign"> ${order_fill_rate} </div>'},
                        
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
                //     name: "Filtering",
                //     type: "remote",
                //     mode: "simple",
                //     filterDialogContainment: "window",
                //     // columnSettings: [
                //     //     {columnKey: 'actions', allowFiltering: false},
                //     //     // {columnKey: 'TotalQuantity', allowFiltering: false},
                //     // ]
                // // },
                // {
                //     name: 'Paging',
                //     type: "local",
                //     pageSize: 10
                // },
                {
                    name: 'Paging',
                    type: 'remote',
                    pageSize: 10,
                    recordCountKey: 'TotalRecordsCount',
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                // {
                //     recordCountKey: 'TotalRecordsCount',
                //     chunkIndexUrlKey: 'page',
                //     chunkSizeUrlKey: 'pageSize',
                //     chunkSize: 10,
                //     name: 'AppendRowsOnDemand',
                //     loadTrigger: 'auto',
                //     type: 'local'
                // }

            ],
            primaryKey: 'OrderId',
            height: '100%',
            width: "100%",
            initialDataBindDepth: 0,
            localSchemaTransform: false,
           

        });

});

// $('#transac_date_from').datepicker({
//     maxDate: 0,
//     onSelect: function () {
//         var select_date = $(this).datepicker('getDate');
//         var nextdayDate = getNextDay(select_date);
//         $('#transac_date_to').datepicker('option', 'minDate', nextdayDate);
//     }
// });

$('#orderverification_summary_report').submit(function (e) {
    this.submit(); // use the native submit method of the form element
    $('#Picker-Verification-summary-report').modal('toggle');
});

    
</script>

@stop
@extends('layouts.footer')