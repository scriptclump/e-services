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
                <div class="caption">{{ trans('inventorycyclecountlabel.heading_1') }}</div>
                <input type="hidden" name="filtered_data_export" id="filtered_data_export">
                <div class="actions">
                    <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document-replanishment" class="btn green-meadow">Import Cycle Count Sheet</a>
                    <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document-stock-take" class="btn green-meadow">Import Stock Take Sheet</a>
                </div>
                
            </div>
            <div class="portlet-body">


                <div class="modal modal-scroll fade" id="upload-document-replanishment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">{{ trans('inventorycyclecountlabel.pop_up_title') }}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                {{ Form::open(array('url' => '/inventoryauditcc/downloadtemplatecc', 'id' => 'downloadexcel-mapping'))}}
                                                <!-- <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn green-meadow btnwidth" id="download-excel">Download Tax Class Template</button>
                                                        </div>
                                                    </div> -->
                                                <!-- <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="checkbox" name="withdata" id='withdata'> WIth Data
                                                    </div>
                                                </div> -->

                                            </div>
                                            <div id='filters'>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                    <select name="warehousenamess_cc" id="warehousenamess_cc" class="form-control" placeholder="{{ trans('inventorycyclecountlabel.dc') }}" required>
                                        <option value="">{{ trans('inventorycyclecountlabel.pop_up_select') }}</option>
                                        @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                        <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                      <button type="submit" class="btn green-meadow" id="download-excel-stock-take">{{ trans('inventorycyclecountlabel.pop_up_download_excel_btn_replanishment') }}</button>  
                                                    </div>
                                                   <div class="col-md-2"><button type="reset" id="reset-button" class="btn green-meadow" title="Reset Form"><i class="fa fa-undo "></i></button></div>
                                                </div>
                                               
                                                    
                                                   

                                            </div>
                                            {{ Form::close() }}
                                            {{ Form::open(['id' => 'replanishment-upload-excel']) }}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">






                                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                            <div>
                                                                <span class="btn default btn-file btn green-meadow btnwidth">
                                                                    <span class="fileinput-new">{{ trans('inventorycyclecountlabel.pop_up_choosefile') }}</span>
                                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Choose Inventory File</span>
                                                                    <input type="file" name="upload_taxfile_replanishment" id="upload_taxfile_replanishment" value="" class="form-control"/>
                                                                </span>
                                                                <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" >
                                                    <div class="form-group">
                                                        <label class="control-label"> </label>
                                                        <button type="button"  class="btn green-meadow" id="excel-upload-button-replanishment">{{ trans('inventorycyclecountlabel.pop_up_upload_btn') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <span id="loader" style="display:none;">Please Wait<img src="/img/spinner.gif" style="width:225px; padding-left:20px;" /></span>
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


                <div class="modal modal-scroll fade" id="upload-document-stock-take" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">{{ trans('inventorycyclecountlabel.pop_up_title_st') }}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                {{ Form::open(array('url' => '/inventoryauditcc/downloadtemplatest', 'id' => 'downloadexcel-mapping-stock-take'))}}


                                            </div>
                                            <div id='filters'>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                    <select name="warehousenamess_st" id="warehousenamess_st" class="form-control" placeholder="{{ trans('inventorycyclecountlabel.dc') }}" required>
                                        <option value="">{{ trans('inventorycyclecountlabel.pop_up_select') }}</option>
                                        @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                        <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                      <button type="submit" class="btn green-meadow" id="download-excel">{{ trans('inventorycyclecountlabel.pop_up_download_excel_btn_replanishment') }}</button>  
                                                    </div>
                                                   <div class="col-md-2"><button type="reset" id="reset-button" class="btn green-meadow" title="Reset Form"><i class="fa fa-undo "></i></button></div>
                                                </div>
                                               
                                                    
                                                   

                                            </div>
                                            {{ Form::close() }}
                                            {{ Form::open(['id' => 'replanishment-upload-excel']) }}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">






                                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                            <div>
                                                                <span class="btn default btn-file btn green-meadow btnwidth">
                                                                    <span class="fileinput-new">{{ trans('inventorycyclecountlabel.pop_up_choosefile') }}</span>
                                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Choose Inventory File</span>
                                                                    <input type="file" name="upload_taxfile_stock_take" id="upload_taxfile_stock_take" value="" class="form-control"/>
                                                                </span>
                                                                <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" >
                                                    <div class="form-group">
                                                        <label class="control-label"> </label>
                                                        <button type="button"  class="btn green-meadow" id="excel-upload-button-stoke-take">{{ trans('inventorycyclecountlabel.inventory_import_btn_stoke_take') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <span id="loader-st" style="display:none;">Please Wait<img src="/img/spinner.gif" style="width:225px; padding-left:20px;" /></span>
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

                    <div><b>Closed Tickets Reports<b></div>
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
                           
                            <div class="col-md-2 closed_ticket_class">
                             <span id="loader_closed_tickets" style="display:none;" ><img src="/img/ajax-loader2.gif" style="padding-left:20px;" /></span>                        
                                <div class="form-group">
                                    <select name="closed_tickets" id="closed_tickets" class="form-control multi-select-search-box SlectBox" placeholder="Select">
                                        
                                    </select>
                                </div>                        
                            </div>

                              <div class="col-md-3">                        
                                <div class="form-group">
                                    <button type="button"  class="btn green-meadow" id="search_button" name="search_button">Download</button>
                                    <button type="button"  class="btn green-meadow" id="search_button1" name="search_button1" onclick="resetFilters();">Reset</button>
                                </div>                        
                            </div>

                            <div class="col-md-3 ">                        
                                <div class="form-group divider">
                                    <!-- resetGrid(); -->
                                    
                                </div>                        
                            </div>



                           
                        </div>

                        <div class="row">

                            <div class="col-md-3" >
                            
                          



                                 
                            </div>        

                            
                           


                        </div>

   

                        <hr />
                    </div>
                <div class="table-scrollable">
                    <table class="inv-title">
                        <tr>
                            <th>All Open Tickets</th>
                        </tr>
                        <tr>
                            <td>
                                <table id="inventorygrid"></table>
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
.divider{
    width:5px;
    height:auto;
    display:inline-block;
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

    .btn-space
    {
        margin-right: 5px;
    }
    .
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
	    th.ui-iggrid-header:nth-child(2){
        text-align: right !important;
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
<!-- <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script> -->

@extends('layouts.footer')
<script type="text/javascript">
    $(document).ready(function () {
        window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
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

        
        $("#toggleFilter").click(function () {
            $("#filters").toggle("fast", function () {});
        });
    

    $("#inventorygrid").igGrid({
            dataSource: '/inventoryauditcc/allopentickets',
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                        {headerText: "Audit Code", key: "link", dataType: "string", width: "15 %"},
                        {headerText: "Ticket Id", key: "bulk_audit_id", dataType: "number", width: "15 %", template: '<div class="textRightAlign"> ${bulk_audit_id} </div>'},
                        {headerText: "Created By", key: "username", dataType: "string", width: "15 %"},
                        {headerText: "Created At", key: "created_at", dataType: "string", width: "15 %"}


                    ],
            features: [

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
            primaryKey: 'bulk_audit_id',
            height: '100%',
            width: "100%",
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            rendered: function (evt, ui) {
                    // resetIggrid();
                }
           

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
    
   

    $("#warehousenamess").change(function()
        {
            var warehousename = $("#warehousenamess").val();
            
            if(warehousename == "")
            {
                alert("Please select a Dc Name");
                return false;
            }
        });

    


    $("#excel-upload-button-replanishment").on('click',function () {
    var token = $("#token_value").val();
    var stn_Doc = $("#upload_taxfile_replanishment")[0].files[0];
    
    
    if (typeof stn_Doc == 'undefined')
    {
        alert("Please select file");
        return false;
    }
    var formData = new FormData();
    formData.append('upload_excel_sheet', stn_Doc);
    var ext = stn_Doc.name.split('.').pop().toLowerCase();
        if($.inArray(ext, ['xls']) == -1) {
            alert("Please choose a valid file");
            return false;
        }
        // console.log(stn_Doc);return false;
    $.ajax({
        type: "POST",
        url: "/inventoryauditcc/excelupload?_token=" + token,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        beforeSend: function () {
            $('#loader').show();
            $("#excel-upload-button-replanishment").attr('disabled', true);
        },
        complete: function () {
            $('#loader').hide();
            $("#excel-upload-button-replanishment").removeAttr('disabled');
        },
        success: function (data)
        {
            /*checking here if the user is not having the access for approval work flow */
           if(data == 0 || data == "0")
            {
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Excel Headers Mis-matched</div></div>');  
            
            }else{
                var datalink = data.linkdownload;
                var LINK = "<a target='_blank' href=/" + datalink + ">View Details</a>";
                var consolidatedmsg = "{{ trans('inventorycyclecountlabel.messages') }}";
                consolidatedmsg = consolidatedmsg.replace('INSERT', data.updated_count);
                consolidatedmsg = consolidatedmsg.replace('ERROR', data.error_count);
                consolidatedmsg = consolidatedmsg.replace('LINK', LINK);
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+ consolidatedmsg +'</div></div>');  
            }
            
            $("#upload_taxfile_replanishment").val("");
            $(".fileinput-filename").html("");
            $("#warehousenamess-replanishment").prop('selectedIndex',0);
            $('#upload-document-replanishment').modal('toggle');
            // $("#inventorygrid").igGrid("dataBind");
            return false;
            
        },
        error:function(jqXHR, textStatus, errorThrown) {
              console.log(textStatus, errorThrown);
            }
    });
});

    $("#excel-upload-button-stoke-take").on('click',function () {
    var token = $("#token_value").val();
    var stn_Doc = $("#upload_taxfile_stock_take")[0].files[0];
    
    
    if (typeof stn_Doc == 'undefined')
    {
        alert("Please select file");
        return false;
    }
    var formData = new FormData();
    formData.append('upload_excel_sheet', stn_Doc);
    var ext = stn_Doc.name.split('.').pop().toLowerCase();
        if($.inArray(ext, ['xls']) == -1) {
            alert("Please choose a valid file");
            return false;
        }
        // console.log(stn_Doc);return false;
    $.ajax({
        type: "POST",
        url: "/inventoryauditcc/exceluploadstocktake?_token=" + token,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
         beforeSend: function () {
            $('#loader-st').show();
            $("#excel-upload-button-stoke-take").attr('disabled', true);
        },
        complete: function () {
            $('#loader-st').hide();
            $("#excel-upload-button-stoke-take").removeAttr('disabled');
        },
        success: function (data)
        {
            /*checking here if the user is not having the access for approval work flow */
           if(data == 0 || data == "0")
            {
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Excel Headers Mis-matched</div></div>');  
            
            }else{
                var datalink = data.linkdownload;
                var LINK = "<a target='_blank' href=/" + datalink + ">View Details</a>";
                var consolidatedmsg = "{{ trans('inventorycyclecountlabel.messages') }}";
                consolidatedmsg = consolidatedmsg.replace('INSERT', data.updated_count);
                consolidatedmsg = consolidatedmsg.replace('ERROR', data.error_count);
                consolidatedmsg = consolidatedmsg.replace('LINK', LINK);
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+ consolidatedmsg +'</div></div>');  
            }
            
            $("#upload_taxfile_stock_take").val("");
            $(".fileinput-filename").html("");
            $("#warehousenamess-replanishment").prop('selectedIndex',0);
            $('#upload-document-stock-take').modal('toggle');
            // $("#inventorygrid").igGrid("dataBind");
            return false;
            
        },
        error:function(jqXHR, textStatus, errorThrown) {
              console.log(textStatus, errorThrown);
            }
    });
});

//  $('#download-excel').submit(function() {
//     // Coding
//     $('#download-excel').modal('toggle');
//     return false;
// });

$('#upload-document-stock-take').on('hidden.bs.modal', function (e) {
    $("#fileinput-filename").html("");
    $("#warehousenamess_st").prop('selectedIndex',0);
});

$('#upload-document-replanishment').on('hidden.bs.modal', function (e) {
    $("#fileinput-filename").html("");
    $("#warehousenamess_cc").prop('selectedIndex',0);

});

//  $('#download-excel').submit(function() {
//     // Coding
//     $('#upload-document').modal('toggle');
//     return false;
// });

$('#downloadexcel-mapping').submit(function (e) {
    this.submit(); // use the native submit method of the form element
    $('#upload-document-replanishment').modal('toggle');
});

$('#downloadexcel-mapping-stock-take').submit(function (e) {
    this.submit(); // use the native submit method of the form element
    $('#upload-document-stock-take').modal('toggle');
});


$("#transac_date_to").change(function(){
    var filters = {}, mainfileters = [];
    var token = $("#token_value").val();
    if($("#transac_date_from").val() == "" || $("#transac_date_to").val() == "")
    {
        alert("Please select date range");
        return false;
    }
    filters['from_date'] = $("#transac_date_from").val();
    filters['to_date'] = $("#transac_date_to").val();

    mainfileters.push(JSON.stringify(filters));

    $.ajax({
        type: "POST",
        url: "/inventoryauditcc/getallclosedtickets?_token=" + token,
        data: {'filters' : mainfileters},
        dataType:'json',

        beforeSend: function () {
            $('.closed_ticket_class').hide();
            $("#loader_closed_tickets").show();

        },
        complete: function () {
            $('.closed_ticket_class').show();
            $("#loader_closed_tickets").hide();
        },
        success: function(data)
        {   
            var ticket_data = $('#closed_tickets option').length;
            console.log("lengthhh "+ticket_data);


            if(ticket_data != 0)
            {
                var num = $('option').length;
                for(var i=0; i<ticket_data; i++){
                    console.log("iteration inside loop "+i);
                    console.log("sumo values "+$('select.SlectBox')[0].sumo);
                   $('select.SlectBox')[0].sumo.remove(0);
                }
            }
            
            // $('select.SlectBox')[0].sumo.reload();
            $.each( data, function( key, value ) {
                // console.log('<option value=' + value + '>' + value + '</option>');
                
                $('select.SlectBox')[0].sumo.add(value);
              // $("#closed_tickets").append('<option value=' + value + '>' + value + '</option>');
            });

        }
    });
});

$("#search_button").click(function(){
    var ticket_number = $("#closed_tickets").val();
    if(ticket_number == null)
    {
        alert("Please select any ticket number");
        return false;
    }
    window.location.href = "/inventoryauditcc/auditapprovaldownloadclosdtkts/"+ticket_number;
});

function resetFilters()
{
    $("#transac_date_from").val("");
    $("#transac_date_to").val("");
    var num = $('#closed_tickets option').length;
    for(var i=0; i<num; i++){
        console.log("iteration inside loop "+i);
        console.log("sumo values "+$('select.SlectBox')[0].sumo);
       $('select.SlectBox')[0].sumo.remove(0);
    }
}

    
</script>

@stop
@extends('layouts.footer')