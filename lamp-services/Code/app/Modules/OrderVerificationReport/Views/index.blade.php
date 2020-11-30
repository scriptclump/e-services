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
                <div class="caption">{{ trans('orderverificationlang.heading_1') }}</div>
                
                <input type="hidden" name="filtered_data_export" id="filtered_data_export">
                <div class="actions">
                    <a href="#" data-id="#" data-toggle="modal" data-target="#orderverification_summary" class="btn green-meadow">{{ trans('orderverificationlang.download_summary_report') }} </a>    
                    <!-- <a href="{{ URL('/orderverificationreport/exportdata') }}" id="del_exe_per_btn_excel" data-id="#" data-toggle="modal" class="btn green-meadow">{{ trans('orderverificationlang.download_order_verification_report') }}</a>     -->
               <a href="javascript:void(0);" id="toggleFilter"><i class="fa fa-filter fa-lg"></i></a>
                </div>
            </div>

            <div class="portlet-body">
                

                <form id="inventory_filters" action="" method="post">

                    <div id="filters" style="display:none;">

                        <div class="row">
                            <div class="col-md-2">                        
                                <div class="form-group">
                                    <div class="input-icon input-icon-sm right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" name="transac_date_from" id="transac_date_from" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('orderverificationlang.filters_date_from') }}">
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-2">                        
                                <div class="form-group">
                                    <div class="input-icon input-icon-sm right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" name="transac_date_to" id="transac_date_to" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('orderverificationlang.filters_date_to') }}">
                                    </div>
                                </div>
                            </div>


                           <div class="col-md-2">                        
                                <div class="form-group">
                                    <select name="crate_num" id="crate_num" class="form-control multi-select-search-box cratenum_reset" multiple="multiple" placeholder="{{ trans('orderverificationlang.crate_num') }}">
                                        @foreach ($all_filters['all_crates'] as  $crate)
                                        <option value="{{ $crate }}">{{ $crate }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>

                           <div class="col-md-2">                        
                                <div class="form-group">
                                    <div class="input-icon input-icon-sm right">
                                       <input type="text" name="order_code" id="order_code" class="form-control" placeholder="{{ trans('orderverificationlang.order_code') }}">
                                    </div>
                                </div>                       
                            </div>

                            <div class="col-md-2">                        
                                <div class="form-group">
                                    <select name="product_sku" id="product_sku" class="form-control multi-select-search-box prod_sku_reset" multiple="multiple" placeholder="{{ trans('orderverificationlang.prod_sku') }}">
                                      @foreach ($all_filters['sku'] as  $sku)
                                        <option value="{{ $sku }}">{{ $sku }}</option>
                                        @endforeach  
                                    </select>
                                </div>                        
                            </div>

                            <div class="col-md-2">                        
                                <div class="form-group">
                                    <select name="product_title" id="product_title" class="form-control multi-select-search-box prodtitle_reset" multiple="multiple" placeholder="{{ trans('orderverificationlang.prod_title') }}">
                                      @foreach ($all_filters['product_titles'] as $pid => $prodtitle)
                                        <option value="{{ $pid }}">{{ $prodtitle }}</option>
                                        @endforeach  
                                    </select>
                                </div>                        
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">                        
                                <div class="form-group">
                                    <select name="dc_names" id="dc_names" class="form-control multi-select-search-box dcnames_reset" multiple="multiple" placeholder="{{ trans('orderverificationlang.dc_name') }}">
                                      @foreach ($all_filters['dc_name'] as $dc_id => $dc_name)
                                        <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                        @endforeach  
                                    </select>
                                </div>                        
                            </div>

                            <div class="col-md-2">                        
                                <div class="form-group">
                                    <select name="hub_names" id="hub_names" class="form-control multi-select-search-box hubnames_reset" multiple="multiple" placeholder="{{ trans('orderverificationlang.hub_name') }}">
                                       @foreach ($all_filters['hub_names'] as $hub_id => $hub_name)
                                        <option value="{{ $hub_id }}">{{ $hub_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">                        
                                <div class="form-group">
                                    <select name="picker_names" id="picker_names" class="form-control multi-select-search-box pickername_reset" multiple="multiple" placeholder="{{ trans('orderverificationlang.pick_name') }}">
                                        @foreach ($all_filters['all_pickers'] as $picker_id => $picker_names)
                                        <option value="{{ $picker_id }}">{{ $picker_names }}</option>
                                        @endforeach  
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">                        
                                <div class="form-group">
                                    <select name="verified_by" id="verified_by" class="form-control multi-select-search-box verified_by_reset" multiple="multiple" placeholder="{{ trans('orderverificationlang.verified_by') }}">
                                      @foreach ($all_filters['verified_users'] as $vid => $verified_by)
                                        <option value="{{ $vid }}">{{ $verified_by }}</option>
                                        @endforeach    
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-2">                        
                                <div class="form-group">
                                    <select name="verification_status" id="verification_status" class="form-control" placeholder="{{ trans('orderverificationlang.verification_status') }}">
                                        <option value="">Verification Status</option>
                                        @foreach ($all_filters['verification_status'] as $ver_id => $verification_status)
                                        <option value="{{ $ver_id }}">{{ $verification_status }}</option>
                                        @endforeach    
                                    </select>
                                </div>
                            </div>



<!--                             <div class="col-md-2">                        
                                <div class="form-group">
                                    <select name="reasons" id="reasons" class="form-control multi-select-search-box" multiple="multiple" placeholder="Reason">
                                        
                                    </select>
                                </div>
                            </div> -->


                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <button type="button"  class="btn green-meadow" id="search_button" name="search_button">{{ trans('orderverificationlang.sub_btn') }}</button>
                                    <button type="button"  class="btn green-meadow" id="search_button1" name="search_button1" onclick="resetFilters();">{{ trans('orderverificationlang.reset_btn') }}</button>
                                </div>                        
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-9">                        
                                        <div class="form-group">
                                            <!-- resetGrid(); -->
                                            <!-- <button type="button"  class="btn green-meadow" id="search_button1" name="search_button1" onclick="resetFilters(); resetGrid();">{{ trans('outflowcyclereport.reset_btn') }}</button> -->
                                        </div>                        
                                    </div>
                                </div>        
                            </div> 
                        </div>
                        <hr />
                    </div>
                </form>
                <table id="grid"></table>
            </div>

                <div class="modal modal-scroll fade" id="orderverification_summary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel"> {{ trans('orderverificationlang.popup_title') }}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                {{ Form::open(array('url' => '/orderverificationreport/summaryreport', 'id' => 'orderverification_summary_report'))}}


                                            </div>
                                            <div id='filters'>
                                                <div class="row">

                                                    <div class="col-md-4">                        
                                                        <div class="form-group">
                                                            <div class="input-icon input-icon-sm right">
                                                                <i class="fa fa-calendar"></i>
                                                                <input type="text" name="transac_date_from1" id="transac_date_from1" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('orderverificationlang.popup_from_date') }} " required>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4">                        
                                                        <div class="form-group">
                                                            <div class="input-icon input-icon-sm right">
                                                                <i class="fa fa-calendar"></i>
                                                                <input type="text" name="transac_date_to1" id="transac_date_to1" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('orderverificationlang.popup_to_date') }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                      <button type="submit" class="btn green-meadow" id="download-excel">{{ trans('orderverificationlang.popup_down_btn') }}</button>  
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
    .verstat{ padding-left: 20px !important;}

    .ui-autocomplete{
        z-index: 10100 !important; top:10px;  height:100px; overflow-y: scroll; overflow-x:hidden; border-top:none!important;
        position:fixed !important;
    }


    .ui-autocomplete li{ border-bottom:1px solid #efefef; padding-top:10px!important; padding-bottom:10px!important; 
    }

    /*#inventorygrid_9_inventory_child_updating_dialog_container{position: absolute !important; top:-20px !important;}*/

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

        $('#transac_date_from1').datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: 0,
            onSelect: function () {
                var select_date = $(this).datepicker('getDate');
                var nextdayDate = getNextDay(select_date);
                $('#transac_date_to1').datepicker('option', 'minDate', nextdayDate);
            }
        });
        $('#transac_date_to1').datepicker({
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


    // $("#del_exe_per_btn_excel").click(function () {
    //     if ($("#transac_date_from").val() == "" || $("#transac_date_to").val() == "")
    //     {
    //         alert("Please select date range");
    //         return false;
    //     }
    // });

    $("#transac_date_from, #transac_date_from1, #transac_date_to, #transac_date_to1").keydown(function () {
        return false;
    });

    $("#search_button").click(function () {

        var filters = {}, mainfileters = [];
        // if ($("#transac_date_from").val() == "" || $("#transac_date_to").val() == "")
        // {
        //     alert("Please select date range");
        //     return false;
        // }
        filters['from_date']            = $("#transac_date_from").val();
        filters['to_date']              = $("#transac_date_to").val();
        filters['crate_number']         = $("#crate_num").val();
        filters['order_code']           = $("#order_code").val();
        filters['prod_sku']             = $("#product_sku").val();
        filters['dc_names']             = $("#dc_names").val();
        filters['hubs']                 = $("#hub_names").val();
        filters['picker_names']         = $("#picker_names").val();
        filters['verified_by']          = $("#verified_by").val();
        filters['verification_status']  = $("#verification_status").val();
        filters['product_title']        = $("#product_title").val();

        // $("#del_exe_per_btn_excel").show();
        mainfileters.push(JSON.stringify(filters));
        $("#del_exe_per_btn_excel").attr("href", "/orderverificationreport/exportdata?filters=" + mainfileters);
        
        if($.trim($("#grid").html()) != ''){
            $("#grid").igGrid("destroy");
        }
        
        $("#grid").igGrid({
            dataSource: '/orderverificationreport/viewdata?filters=' + mainfileters,
            responseDataKey: "results",
            columns: [
                {headerText: "Crate No.", key: "crate_num", dataType: "string", width: "150px"},
                {headerText: "DC Name", key: "dcname", dataType: "string", width: "132px"},
                {headerText: "Hub Name", key: "hub_name", dataType: "string", width: "145px"},
                {headerText: "Order Code", key: "order_code", dataType: "string", width: "130px"},
                {headerText: "Order Date", key: "order_date", dataType: "string", width: "127px"},
                {headerText: "Picker Name", key: "pickername", dataType: "string", width: "200px"},
                {headerText: "Picking Time", key: "picking_time", dataType: "string", width: "127px"},
                {headerText: "Verified By", key: "verifier_name", dataType: "string", width: "150px"},
                {headerText: "Verification Time", key: "verification_time", dataType: "string", width: "127px"},
                {headerText: "Verification Status", key: "verification_status", dataType: "string", width: "120px",template: '<div class="verstat"> ${verification_status} </div>'},
                {headerText: "Product SKU", key: "sku", dataType: "string", width: "100px"},
                {headerText: "Product Title", key: "product_title", dataType: "string", width: "200px"},
                {headerText: "Reason", key: "reason", dataType: "string", width: "150px", template: '<div class="verstat"> ${reason} </div>'},
                {headerText: "Quantity", key: "wrong_qty", dataType: "number", width: "100px",template: '<div class="textRightAlign"> ${wrong_qty} </div>'},
                {headerText: "File Path", key: "file_path", dataType: "string", width: "200px"}
            ],
            features: [
                {
                    name: "Sorting",
                    sortingDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'pickername', allowSorting: false},
                        {columnKey: 'verifier_name', allowSorting: false},
                        {columnKey: 'verifier_status', allowSorting: false},
                        {columnKey: 'dcname', allowSorting: false},
                        {columnKey: 'verification_status', allowSorting: false},
                        {columnKey: 'product_title', allowSorting: false},
                        {columnKey: 'hub_name', allowSorting: false},
                    ]
                },
                // {
                //     recordCountKey: 'TotalRecordsCount',
                //     chunkIndexUrlKey: 'page',
                //     chunkSizeUrlKey: 'pageSize',
                //     chunkSize: 20,
                //     name: 'AppendRowsOnDemand',
                //     loadTrigger: 'auto',
                //     type: 'remote'
                // },
                {
                    name: 'Paging',
                    type: 'remote',
                    pageSize: 20,
                    recordCountKey: 'TotalRecordsCount',
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                {
                    name: "ColumnFixing",
                    fixingDirection: "left",
                    columnSettings: [
                        {
                            columnKey: "crate_num",
                            isFixed: true,
                            allowFixing: false
                        },
                        {
                            columnKey: "dcname",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "hub_name",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "order_code",
                            isFixed: true,
                            allowFixing: false
                        },
                        {
                            columnKey: "order_date",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "pickername",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "picking_time",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "verifier_name",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "verification_status",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "verification_time",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "sku",
                            isFixed: true,
                            allowFixing: false
                        },
                        {
                            columnKey: "product_title",
                            isFixed: true,
                            allowFixing: false
                        },
                        {
                            columnKey: "reason",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "wrong_qty",
                            isFixed: false,
                            allowFixing: false
                        }
                    ]
                }
            ],
            width: '100%',
            height: '600px'
        });
    });

    function resetFilters() {
        $("#toggleFilter_export").attr("href", "getExport");
        var crate_no = [], prod_sku = [], dcnames = [], hub_names = [], picker_names =[], verified_by = [], product_title = [];
        /* crate num filter reset*/
        $('#crate_num option:selected').each(function () {
            crate_no.push($(this).index());
        });
        for (var i = 0; i < crate_no.length; i++) {
            $('.cratenum_reset')[0].sumo.unSelectItem(crate_no[i]);
        }

        /* product sku filter reset*/
        $('#product_sku option:selected').each(function () {
            prod_sku.push($(this).index());
        });
        for (var i = 0; i < prod_sku.length; i++) {
            $('.prod_sku_reset')[0].sumo.unSelectItem(prod_sku[i]);
        }

        /* DC Name filter reset*/
        $('#dc_names option:selected').each(function () {
            dcnames.push($(this).index());
        });
        for (var i = 0; i < dcnames.length; i++) {
            $('.dcnames_reset')[0].sumo.unSelectItem(dcnames[i]);
        }

        /* Hub Name filter reset*/
        $('#hub_names option:selected').each(function () {
            hub_names.push($(this).index());
        });
        for (var i = 0; i < hub_names.length; i++) {
            $('.hubnames_reset')[0].sumo.unSelectItem(hub_names[i]);
        }

        /* Picker Name filter reset*/
        $('#picker_names option:selected').each(function () {
            picker_names.push($(this).index());
        });
        for (var i = 0; i < picker_names.length; i++) {
            $('.pickername_reset')[0].sumo.unSelectItem(picker_names[i]);
        }

        /*verified_by filter reset*/
        $('#verified_by option:selected').each(function () {
            verified_by.push($(this).index());
        });
        for (var i = 0; i < verified_by.length; i++) {
            $('.verified_by_reset')[0].sumo.unSelectItem(verified_by[i]);
        }

        /* Product Title filter reset*/
        $('#product_title option:selected').each(function () {
            product_title.push($(this).index());
        });
        for (var i = 0; i < product_title.length; i++) {
            $('.prodtitle_reset')[0].sumo.unSelectItem(product_title[i]);
        }

        




        $('#transac_date_from').val('');
        $('#transac_date_to').val('');
        $("#order_code").val('');
        $('#verification_status').prop('selectedIndex',0);
        // $("#grid").igGrid("destroy");
        var  mainURL = "/orderverificationreport/viewdata";
        $("#grid").igGrid({dataSource: mainURL});
    }


    $(function () {

            $("#grid").igGrid({
            dataSource: '/orderverificationreport/viewdata',
            responseDataKey: "results",
            columns: [
                {headerText: "Crate No.", key: "crate_num", dataType: "string", width: "150px"},
                {headerText: "DC Name", key: "dcname", dataType: "string", width: "132px"},
                {headerText: "Hub Name", key: "hub_name", dataType: "string", width: "145px"},
                {headerText: "Order Code", key: "order_code", dataType: "string", width: "130px"},
                {headerText: "Order Date", key: "order_date", dataType: "string", width: "127px"},
                {headerText: "Picker Name", key: "pickername", dataType: "string", width: "200px"},
                {headerText: "Picking Time", key: "picking_time", dataType: "string", width: "127px"},
                {headerText: "Verified By", key: "verifier_name", dataType: "string", width: "150px"},
                {headerText: "Verification Time", key: "verification_time", dataType: "string", width: "127px"},
                {headerText: "Verification Status", key: "verification_status", dataType: "string", width: "120px",template: '<div class="verstat"> ${verification_status} </div>'},
                {headerText: "Product SKU", key: "sku", dataType: "string", width: "100px"},
                {headerText: "Product Title", key: "product_title", dataType: "string", width: "200px"},
                {headerText: "Reason", key: "reason", dataType: "string", width: "150px", template: '<div class="verstat"> ${reason} </div>'},
                {headerText: "Quantity", key: "wrong_qty", dataType: "number", width: "100px",template: '<div class="textRightAlign"> ${wrong_qty} </div>'},
                {headerText: "File Path", key: "file_path", dataType: "string", width: "200px"}
            ],
            features: [
                {
                    name: "Sorting",
                    sortingDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'pickername', allowSorting: false},
                        {columnKey: 'verifier_name', allowSorting: false},
                        {columnKey: 'verifier_status', allowSorting: false},
                        {columnKey: 'dcname', allowSorting: false},
                        {columnKey: 'verification_status', allowSorting: false},
                        {columnKey: 'product_title', allowSorting: false},
                        {columnKey: 'hub_name', allowSorting: false},
                    ]
                },
                // {
                //     recordCountKey: 'TotalRecordsCount',
                //     chunkIndexUrlKey: 'page',
                //     chunkSizeUrlKey: 'pageSize',
                //     chunkSize: 20,
                //     name: 'AppendRowsOnDemand',
                //     loadTrigger: 'auto',
                //     type: 'remote'
                // },
                {
                    name: 'Paging',
                    type: 'remote',
                    pageSize: 20,
                    recordCountKey: 'TotalRecordsCount',
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                },
                {
                    name: "ColumnFixing",
                    fixingDirection: "left",
                    columnSettings: [
                        {
                            columnKey: "crate_num",
                            isFixed: true,
                            allowFixing: false
                        },
                        {
                            columnKey: "dcname",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "hub_name",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "order_code",
                            isFixed: true,
                            allowFixing: false
                        },
                        {
                            columnKey: "order_date",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "pickername",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "picking_time",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "verifier_name",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "verification_status",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "verification_time",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "sku",
                            isFixed: true,
                            allowFixing: false
                        },
                        {
                            columnKey: "product_title",
                            isFixed: true,
                            allowFixing: false
                        },
                        {
                            columnKey: "reason",
                            isFixed: false,
                            allowFixing: false
                        },
                        {
                            columnKey: "wrong_qty",
                            isFixed: false,
                            allowFixing: false
                        }
                    ]
                }
            ],
            width: '100%',
            height: '600px'
        });
    });
autosuggest();

function autosuggest(){
        $( "#order_code" ).autocomplete({
             source: '/orderverificationreport/ordercodes',
             minLength: 2,
             params: { entity_type:$('#supplier_list').val() },
             select: function( event, ui ) {
                  if(ui.item.label=='No Result Found'){
                     event.preventDefault();
                  }
                  $('#addproduct_id').val(ui.item.product_id);
                  $('#prod_brand').text(ui.item.brand);
                  $('#prod_sku').text(ui.item.sku);
                  $('#prod_mrp').text(ui.item.mrp);
             }
         });
    }

$("#toggleFilter").click(function () {
    $("#filters").toggle("fast", function () {});
});
    /*Closing Modal Box*/
$('#orderverification_summary_report').submit(function (e) {
    this.submit(); // use the native submit method of the form element
    $('#orderverification_summary').modal('toggle');
});
</script>



@stop
@extends('layouts.footer')