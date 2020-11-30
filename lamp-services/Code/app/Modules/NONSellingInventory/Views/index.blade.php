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
                <div class="caption"> {{trans('nonSellable.heading_1')}}</div>
                <input type="hidden" name="filtered_data_export" id="filtered_data_export">
                <div class="actions">
                    
                    <!-- <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document" class="btn green-meadow">{{ trans('inventorylabel.filters.inventory_import_btn') }}</a>     -->
                    
                <span  data-placement="top"  class="tooltips"><a href="{{ URL('nonsellinginventory/exportData') }}" class="btn green-meadow" id="toggleFilter_export" style="display:none;">{{ trans('nonSellable.export_button') }}</a></span>
                
                
                <!-- <a href="javascript:void(0);" id="toggleFilter"><i class="fa fa-filter fa-lg"></i></a> -->

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
                                            <input type="text" name="transac_date_from" id="transac_date_from" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{trans('nonSellable.fromDate')}}">
                                        </div>
                                    </div>                        
                                </div>
                                <div class="col-md-2">                        
                                    <div class="form-group">
                                        <div class="input-icon input-icon-sm right">
                                            <i class="fa fa-calendar"></i>
                                            <input type="text" name="transac_date_to" id="transac_date_to" class="form-control end_date dp" value="" autocomplete="off" placeholder="{{trans('nonSellable.toDate')}}">
                                        </div>
                                    </div>                        
                                </div>

                            <div class="col-md-3">                        
                                    <div class="form-group">
                                        <select name="field_force_users" id="field_force_users" class="form-control multi-select-search-box field_force_reset" multiple="multiple" placeholder="{{trans('nonSellable.fieldForceUsersDropDown')}}">
                                         @foreach ($fieldforceusers as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach   
                                        </select>
                                    </div>
                                </div>

                            <div class="col-md-3">                        
                                    <div class="form-group">
                                        <select name="places" id="places" class="form-control multi-select-search-box place_reset" multiple="multiple" placeholder="{{trans('nonSellable.areaDropDown')}}">
                                         @foreach ($places as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach   
                                        </select>
                                    </div>
                                </div>

                                 <div class="col-md-2 text-right">
                                <input type="button" value="{{trans('nonSellable.filterBtn')}}" class="btn btn-success" onclick="filterGrid();">
                                <input type="button" value="{{trans('nonSellable.resetBtn')}}" class="btn btn-success" onclick="resetFilters();">
                            </div>

                        </div>
                        
                        
                        
                       
                        <hr />
                    </div>
                </form>

                

               




                <div class="table-scrollable">
                    <table class="inv-title"  style="background:#F2F2F2;">
                        <thead>
                        <tr bgcolor="#F2F2F2" height="30px" align="center">
                            <th bgcolor="#F2F2F2" align="center" class="titlecenter">{{trans('nonSellable.gridHeading')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <table id="nonsellableproducts"></table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
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

.titlecenter{text-align: center !important;}
.table-scrollable {
   
    background: #F2F2F2 !important;
}
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

    th.ui-iggrid-header:nth-child(3), th.ui-iggrid-header:nth-child(4), th.ui-iggrid-header:nth-child(5), th.ui-iggrid-header:nth-child(6){
        /*text-align: right !important;*/
    }

    table[data-childgrid="true"] th.ui-iggrid-header{
        text-align: left !important;
    }

    .inv-title {
        text-align: center; margin: 0 auto; background-color: #F2F2F2; height: 40px;
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
	
	#inventorygrid_9_inventory_child_updating_dialog_container{position: absolute !important; top:-20px !important;}
	
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
<!-- <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript"></script> -->
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
<!-- Inventory Grid Component  -->
<script src="{{ URL::asset('assets/admin/pages/scripts/InventoryModule/inventoryGrid.js') }}" type="text/javascript"></script>
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
        maxDate:0,
            onSelect: function() {
                var select_date = $(this).datepicker('getDate');
                var nextdayDate = getNextDay(select_date);
                $('#transac_date_to').datepicker('option', 'minDate', nextdayDate);        
            }
        });

    $('#transac_date_to').datepicker({
        maxDate:'+0D',
    });
    });
    // $(".dp").datepicker({
    //     autoclose: true,
    //     dateFormat: 'yy-mm-dd'
    // });

    function getNextDay(select_date){
    select_date.setDate(select_date.getDate());
    var setdate = new Date(select_date);
    var nextdayDate = zeroPad((setdate.getMonth()+1),2)+'/'+zeroPad(setdate.getDate(),2)+'/'+setdate.getFullYear();
    return nextdayDate;
}
function zeroPad(num, count) {
    var numZeropad = num + '';
    while (numZeropad.length < count) {
        numZeropad = "0" + numZeropad;
    }
    return numZeropad;
}


    function filterGrid()
    {
        var mainFilters = [], eachFilter = {};
        var startdate = $("#transac_date_from").val();
        var enddate = $("#transac_date_to").val();
        if((startdate == "" && enddate == "") || (startdate == "" || enddate == ""))
        {
            alert("{{trans('nonSellable.dateserror_message')}}");
            return false;
        }
        var fieldforceuser = $("#field_force_users").val();
        var place = $("#places").val();

        if(fieldforceuser == "")
        {
            fieldforceuser = 0;
        }

         if(place == "")
        {
            place = 0;
        }


         eachFilter["startdate"] = startdate;
         eachFilter["enddate"] = enddate;
         eachFilter["fieldforceuser"] = fieldforceuser;
         eachFilter["area"] = place;

        mainFilters.push(JSON.stringify(eachFilter));
        $("#toggleFilter_export").show();
        $("#toggleFilter_export").attr("href", "/nonsellinginventory/exportData?filters="+mainFilters);

           $("#nonsellableproducts").igGrid({
            dataSource: '/nonsellinginventory/filteredData?filters=' + mainFilters,
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                        {headerText: "Product Id", key: "product_id", dataType: "string", width: "10%", template: '<div class="textRightAlign"> ${product_id} </div>'},
                        {headerText: "Product Title", key: "product_title", dataType: "string", width: "60%", template: '<div class="textLeftAlign"> ${product_title} </div>'},
                        {headerText: "SKU", key: "sku", dataType: "string", width: "10%", template: '<div class="textLeftAlign"> ${sku} </div>'},
                        {headerText: "MRP", key: "mrp", dataType: "string",width: "10%", template: '<div class="textRightAlign"> ${mrp} </div>'},
                        {headerText: "SP", key: "esp", dataType: "string", width: "10%", template: '<div class="textRightAlign"> ${esp} </div>'},
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
                // },
                {
                    name: 'Paging',
                    type: "local",
                    pageSize: 10
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
            primaryKey: 'product_id',
            height: '100%',
            width: "100%",
            initialDataBindDepth: 0,
            localSchemaTransform: false,
           

        });
        // console.log("testing"+mainFilters);
    }

    function resetFilters()
    {
        var fieldforce = [], place = [];

        $("#transac_date_from").val('');
        $("#transac_date_to").val('');
        //Reseting the field force field
         $('#field_force_users option:selected').each(function () {
            fieldforce.push($(this).index());
        });
        for (var i = 0; i < fieldforce.length; i++) {
            $('.field_force_reset')[0].sumo.unSelectItem(fieldforce[i]);
        }
            //Reseting the Place field
        $('#places option:selected').each(function () {
            place.push($(this).index());
        });
        for (var i = 0; i < place.length; i++) {
            $('.place_reset')[0].sumo.unSelectItem(place[i]);
        }

        // $("#nonsellableproducts").html("");
        $("#toggleFilter_export").css("display", "none");
        $("#nonsellableproducts").igGrid('destroy');
    }
$("#transac_date_from, #transac_date_to").keydown(function() {
return false;
});
    
</script>

@stop
@extends('layouts.footer')