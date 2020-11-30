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
                <div class="caption">{{ trans('outflowcyclereport.mainLabel') }}</div>
                <input type="hidden" name="filtered_data_export" id="filtered_data_export">
                <div class="actions">
                    <a style="display:none;" href="{{ URL('/outflowcyclereport/exportgrid') }}" id="del_exe_per_btn_excel" data-id="#" data-toggle="modal" class="btn green-meadow">{{ trans('outflowcyclereport.excelReport') }}</a>    
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
                                    <input type="text" name="transac_date_from" id="transac_date_from" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('outflowcyclereport.filters_date') }}">
                                </div>
                            </div>
                            </div>


                              <div class="col-md-2">                        
                                <div class="form-group">
                                <div class="input-icon input-icon-sm right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" name="transac_date_to" id="transac_date_to" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('outflowcyclereport.filters_date_to') }}">
                                </div>
                            </div>
                            </div>
                            
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="all_sales_officers" id="all_sales_officers" class="form-control multi-select-search-box all_sales_officers_reset" multiple="multiple" placeholder="{{ trans('outflowcyclereport.filters_salesofficer') }}">
                                     	 @foreach ($options['allsalesofficers'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach   
                                    </select>
                                </div>                        
                            </div>

                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="all_delivery_executives" id="all_delivery_executives" class="form-control multi-select-search-box all_delivery_executives_reset" multiple="multiple" placeholder="{{ trans('outflowcyclereport.filters_delivery_executives') }}">
                                     	 @foreach ($options['alldeliveryexecutives'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach   
                                    </select>
                                </div>                        
                            </div>




                           
                        </div>

                        <div class="row">

                            <div class="col-md-3" style="display:none;">                        
                                <div class="form-group">
                                    <select name="all_retailers" id="all_retailers" class="form-control multi-select-search-box all_retailers_reset" multiple="multiple" placeholder="{{ trans('outflowcyclereport.filters_retailers') }}">
                                         @foreach ($options['allretailers'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach   
                                    </select>
                                </div>                        
                            </div>

                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="area" id="area" class="form-control multi-select-search-box area_reset" multiple="multiple" placeholder="{{ trans('outflowcyclereport.filters_area') }}">
                                     	@foreach ($options['allareas'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach    
                                    </select>
                                </div>                        
                            </div>

                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="beat" id="beat" class="form-control multi-select-search-box beat_reset" multiple="multiple" placeholder="{{ trans('outflowcyclereport.filters_beat') }}">
                                     @foreach ($options['allbeats'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach 	  
                                    </select>
                                </div>                        
                            </div>
                            
                            <div class="col-md-3">
                            <div class="row">
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <button type="button"  class="btn green-meadow" id="search_button" name="search_button">{{ trans('outflowcyclereport.submit_btn') }}</button>
                                </div>                        
                            </div>

                            <div class="col-md-9">                        
                                <div class="form-group">
                                    <!-- resetGrid(); -->
                                    <button type="button"  class="btn green-meadow" id="search_button1" name="search_button1" onclick="resetFilters(); resetGrid();">{{ trans('outflowcyclereport.reset_btn') }}</button>
                                </div>                        
                            </div>

                            </div>        
                            </div>        

                            
                           


                        </div>

   

                        <hr />
                    </div>
                </form>

                

              




                <div class="table-scrollable">
                    <table class="inv-title">
                        <tr>
                            <th>{{ trans('outflowcyclereport.grid_header') }}</th>
                        </tr>
                        <tr>
                            <td>
                                <table id="del_exe_per_rep_grid"></table>
                            </td>
                        </tr>
                    </table>
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
<!--Bootstrap dataepicker JavaScript Files-->


@extends('layouts.footer')
<script type="text/javascript">
    $(document).ready(function () {
        window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..', selectAll: true});
        window.groups_eg_g = $('.groups_eg_g').SumoSelect({search: true});
        

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
    

$("#del_exe_per_btn_excel").click(function(){
       if($("#transac_date_from").val() == "" || $("#transac_date_to").val() == "")
        {
            alert("Please select date range");
            return false;
        } 
});

$("#search_button").click(function(){
    $("#del_exe_per_btn_excel").show();
	var filters = {}, mainfileters = [];
    if($("#transac_date_from").val() == "" || $("#transac_date_to").val() == "")
    {
        alert("Please select date range");
        return false;
    }
	filters['date'] = $("#transac_date_from").val();
    filters['to_date'] = $("#transac_date_to").val();
	filters['sales_officers'] = $("#all_sales_officers").val();
	filters['delivery_executives'] = $("#all_delivery_executives").val();
	filters['retailers'] = $("#all_retailers").val();
	filters['area'] = $("#area").val();
	filters['beat'] = $("#beat").val();
	// console.log(filters);

	mainfileters.push(JSON.stringify(filters));

    if($.trim($("#del_exe_per_rep_grid").html())!='') {
        $("#del_exe_per_rep_grid").data("igGridPaging").options.currentPageIndex = 0;
    }

// console.log(mainfileters);

// if($("#transac_date_from").val().length !== 0 || $("#transac_date_to").val().length !== 0 || $("#all_sales_officers").val() !== null || $("#all_delivery_executives").val() !== null || $("#all_retailers").val() !== null || $("#area").val() !== null || $("#beat").val() !== null)
// {
//     var iggridCount = $("#del_exe_per_rep_grid").igGrid("totalRecordsCount");
//     if(iggridCount > 0)
//     {
//         console.log("grid is not empty");
//         $("#del_exe_per_rep_grid").data("igGridPaging").options.currentPageIndex = 0;    
//     }
    
//     // $("#del_exe_per_rep_grid").igGrid("dataBind");
// }

	$("#del_exe_per_btn_excel").attr("href", "/outflowcyclereport/exportgrid?filters=" + mainfileters);
           $("#del_exe_per_rep_grid").igGrid({
            dataSource: '/outflowcyclereport/griddata?filters=' + mainfileters,
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                        {headerText: "SO No", key: "SO_num", dataType: "date", width: "150px"},
                        {headerText: "SO Date", key: "SO_Date", dataType: "string", width: "150px"},

                        {headerText: "SO Created By", key: "created_By", dataType: "string", width: "150px"},
                        {headerText: "Retailer Name", key: "retailer_name", dataType: "string", width: "150px"},
                        
                        {headerText: "Area", key: "area_name", dataType: "string",width: "100px", template: '<div class="textLeftAlign"> ${area_name} </div>'},
                        {headerText: "Beat", key: "beat_name", dataType: "number", width: "100px", template: '<div class="textLeftAlign"> ${beat_name} </div>'},
                        
                        {headerText: "Hub Name", key: "hub_name", dataType: "string", width: "100px", template: '<div class="textLeftAlign"> ${hub_name} </div>'},
                        {headerText: "Order Status", key: "order_status", dataType: "string", width: "100px", template: '<div class="textLeftAlign"> ${order_status} </div>'},
                        {headerText: "Product Code", key: "product_code", dataType: "string", width: "100px", template: '<div class="textLeftAlign"> ${product_code} </div>'},
                        {headerText: "Product Description", key: "product_description", dataType: "string",width: "250px",  template: '<div class="textLeftAlign"> ${product_description} </div>'},
                        {headerText: "MRP", key: "mrp", dataType: "number", width: "60px", template: '<div class="textRightAlign"> ${mrp} </div>'},

                        {headerText: "SO Qty", key: "SO_Qty", dataType: "string", width: "60px", template: '<div class="textRightAlign"> ${SO_Qty} </div>'},
                        {headerText: "SO Val", key: "SO_val", dataType: "string",width: "100px", template: '<div class="textRightAlign"> ${SO_val} </div>'},
                        {headerText: "Picked Date", key: "picked_Date", dataType: "string", width: "100px"},
						{headerText: "Picked Qty", key: "Picked_qty", dataType: "string", width: "100px", template: '<div class="textRightAlign"> ${Picked_qty} </div>'},
                        {headerText: "Picked by", key: "picked_by", dataType: "string", width: "100px", template: '<div class="textLeftAlign"> ${picked_by} </div>'},

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
            primaryKey: 'unique',
            height: '100%',
            width: "100%",
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            rendered: function (evt, ui) {
                    // resetIggrid();
                }
           

        });

});

function resetIggrid()
{
    $("#del_exe_per_rep_grid").data("igGridPaging").options.currentPageIndex = 0;   
}

    function resetGrid()
    {
        console.log("reset grid filters");
        $("#del_exe_per_btn_excel").hide();
        $("#del_exe_per_rep_grid").igGrid("destroy");
    }

    function resetFilters() {

        console.log("you are in reset filters");
        var allsalesofficers = [], deliveryexecutives = [], all_reatailers = [], area = [], beat = [];

        // $("#toggleFilter_export").attr("href", "getExport");

        $('#all_sales_officers option:selected').each(function () {
            allsalesofficers.push($(this).index());
        });
        for (var i = 0; i < allsalesofficers.length; i++) {
            $('.all_sales_officers_reset')[0].sumo.unSelectItem(allsalesofficers[i]);
        }



        $('#all_delivery_executives option:selected').each(function () {
            deliveryexecutives.push($(this).index());
        });
        for (var i = 0; i < deliveryexecutives.length; i++) {
            $('.all_delivery_executives_reset')[0].sumo.unSelectItem(deliveryexecutives[i]);
        }


        $('#all_retailers option:selected').each(function () {
            all_reatailers.push($(this).index());
        });
        for (var i = 0; i < all_reatailers.length; i++) {
            $('.all_retailers_reset')[0].sumo.unSelectItem(all_reatailers[i]);
        }


        $('#area option:selected').each(function () {
            area.push($(this).index());
        });
        for (var i = 0; i < area.length; i++) {
            $('.area_reset')[0].sumo.unSelectItem(area[i]);
        }

        $('#beat option:selected').each(function () {
            beat.push($(this).index());
        });
        for (var i = 0; i < beat.length; i++) {
            $('.beat_reset')[0].sumo.unSelectItem(beat[i]);
        }



        $('#transac_date_from').val('');
        $('#transac_date_to').val('');
        
        // $('#is_sellable').prop('selectedIndex',0);
        // $('#cp_enabled').prop('selectedIndex',0);
        // $('#is_sellable').removeAttr('checked');
        // $('#cp_enabled').removeAttr('checked');
    }    
</script>



@stop
@extends('layouts.footer')