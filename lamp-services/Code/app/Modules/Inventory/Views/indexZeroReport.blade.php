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
                <div class="caption">{{ trans('zeroSOHLabels.heading_1') }}</div>
                <input type="hidden" name="filtered_data_export" id="filtered_data_export">
                <div class="actions">
                <!-- @if($role_access == '1') -->
                <span data-original-title="Export to Excel" data-placement="top" class="tooltips"><a href="{{ URL('inventory/getExportZeroReport') }}" id="toggleFilter_export"><i class="fa fa-file-excel-o fa-lg" id="export_excel"></i></a></span>
                <!-- @endif -->
                <a href="javascript:void(0);" id="toggleFilter"><i class="fa fa-filter fa-lg"></i></a>

                </div>
                
            </div>
            <div class="portlet-body">
                <form id="inventory_filters" action="" method="post">	
                    <div id="filters" style="display:none;">
                        <div class="row">
                            <!-- <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="dc_name" id="dc_name" class="form-control multi-select-box dc_reset" multiple="multiple" placeholder="{{ trans('inventorylabel.filters.dc') }}">
                                        @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                        <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="manf_name" id="manf_name" class="form-control multi-select-search-box manf_name_reset" multiple="multiple" placeholder="{{ trans('zeroSOHLabels.manufacturer') }}">
                                        @foreach ($filter_options['manfacturer_name'] as $manf_id => $manf_name)
                                        <option value="{{ $manf_id }}">{{ $manf_name }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="category" id="category" class="form-control multi-select-search-box category_reset" multiple="multiple" placeholder="{{ trans('zeroSOHLabels.category') }}">
                                        {!!html_entity_decode($filter_options['category_name'])!!}
                                    </select>
                                </div>                        
                            </div>
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="brand" id="brand" class="form-control multi-select-search-box brand_reset" multiple="multiple" placeholder="{{ trans('zeroSOHLabels.brand') }}">
                                        {!!html_entity_decode($filter_options['brand_name'])!!}
                                    </select>
                                </div>                        
                            </div>

                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="product_char" id="product_char" class="form-control multi-select-box product_char_reset" multiple="multiple" placeholder="{{ trans('zeroSOHLabels.characteristics') }}">
                                        <option value="perishable">Perishable</option>
                                        <option value="flammable">Flammable</option>
                                        <option value="hazardous">Hazardous</option>
                                        <option value="odour">Odour</option>
                                        <option value="fragile">Fragile</option>
                                    </select>
                                </div>                        
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="product_titles" id="product_titles" class="form-control multi-select-search-box product_titles_reset" multiple="multiple" placeholder="{{ trans('inventorylabel.filters.product_title') }}">
                                        @foreach ($filter_options['product_titles'] as $prod_id => $prod_titles)
                                        <option value="{{ $prod_id }}">{{ $prod_titles }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="sku" id="sku" class="form-control multi-select-search-box sku_reset" multiple="multiple" placeholder="{{ trans('inventorylabel.filters.sku') }}">
                                        @foreach ($filter_options['sku'] as $sku_code)
                                        <option value="{{ $sku_code }}">{{ $sku_code }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="kvi" id="kvi" class="form-control multi-select-box kvi_reset" multiple="multiple" placeholder="{{ trans('inventorylabel.filters.kvi') }}">
                                        @foreach ($filter_options['kvi'] as $kvi_id => $kvi)
                                        <option value="{{ $kvi }}">{{ $kvi }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="ean_upc" id="ean_upc" class="form-control multi-select-search-box ean_upc_reset" multiple="multiple" placeholder="{{ trans('inventorylabel.filters.upc') }}">
                                        @foreach ($filter_options['upc_ean'] as $upc_ean)
                                        <option value="{{ $upc_ean }}">{{ $upc_ean }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>
                           
                            
                        </div> -->
                        <div class="row">
                            

                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="product_form" id="product_form" class="form-control multi-select-box product_form_reset" multiple="multiple" placeholder="{{ trans('zeroSOHLabels.form') }}">
                                        @foreach ($filter_options['product_form'] as $product_form_id => $product_form)
                                        <option value="{{ $product_form_id }}">{{ $product_form }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>

                             <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="number" min ="0" name="shelf_life" id="shelf_life" class="form-control" placeholder="{{ trans('zeroSOHLabels.shelf') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select name="shelf_life_uom" id="shelf_life_uom" class="form-control multi-select-box" placeholder="{{ trans('zeroSOHLabels.shelf_uom') }}">
                                                <option value="nodata">Shelf Life UOM</option>
                                                @foreach ($filter_options['shelflife_uom'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">

                                <div class="form-group">
                                    <input name="inv_age" id="inv_age" class="form-control" placeholder="{{ trans('zeroSOHLabels.aging') }}" />
                                </div>                        
                            </div>

                            <div class="col-md-3 text-right">
                                <input type="button" value="{{ trans('zeroSOHLabels.button1') }}" class="btn btn-success" onclick="filterGrid();">
                                <input type="button" value="{{ trans('zeroSOHLabels.button2') }}" class="btn btn-success" onclick="resetFilters(); resetGrid();">
                            </div>
                            
                        </div>
                        <!-- <div class="row">
                            <div class="col-md-3"> 
                                <label>{{ trans('inventorylabel.filters.fresh') }}</label>
                                <input type="number" id="fresh_min_range" class="range-min-css" value="" step="1" min="0" max="100">
                                <div class="slider">
                                    <div id="fresh_slider"></div>
                                </div>
                                <input type="number" id="fresh_max_range" class="range-max-css" value="" step="1" min="0" max="100">
                            </div>

                            <div class="col-md-3"> 
                                <label>{{ trans('inventorylabel.filters.mrp') }}</label>
                                <input type="number" id="mrp_min_range" class="range-min-css" value="" step="1" min="{{ $filter_options['min_mrp'] }}" max="{{ $filter_options['max_mrp'] }}" data-min="{{ $filter_options['min_mrp'] }}">
                                <input type="number" id="mrp_min_range" class="range-min-css" value="" step="1" min="0" max="{{ $filter_options['max_mrp'] }}" data-min="0">
                                <div class="slider">
                                    <div id="mrp_slider"></div>
                                </div>
                                <input type="number" id="mrp_max_range" class="range-max-css" value="" step="1" min="{{ $filter_options['min_mrp'] }}" max="{{ $filter_options['max_mrp'] }}" data-max="{{ $filter_options['max_mrp'] }}">
                            </div>
                            <div class="col-md-3"> 
                                <label>{{ trans('inventorylabel.filters.soh') }}</label>
                                <input type="number" id="soh_min_range" class="range-min-css" value="" step="1" min="{{ $filter_options['min_soh'] }}" max="{{ $filter_options['max_soh'] }}" data-min="{{ $filter_options['min_soh'] }}">
                                <input type="number" id="soh_min_range" class="range-min-css" value="" step="1" min="0" max="{{ $filter_options['max_soh'] }}" data-min="0">
                                <div class="slider">
                                    <div id="soh_slider"></div>
                                </div>
                                <input type="number" id="soh_max_range" class="range-max-css" value="" step="1" min="{{ $filter_options['min_soh'] }}" max="{{ $filter_options['max_soh'] }}" data-max="{{ $filter_options['max_soh'] }}">
                            </div>
                            <div class="col-md-3"> 
                                <label>{{ trans('inventorylabel.filters.map') }}</label>
                                <input type="number" id="map_min_range" class="range-min-css" value="" step="1" min="{{ $filter_options['min_map'] }}" max="{{ $filter_options['max_map'] }}" data-min="{{ $filter_options['min_map'] }}">
                                <input type="number" id="map_min_range" class="range-min-css" value="" step="1" min="0" max="{{ $filter_options['max_map'] }}" data-min="0">
                                <div class="slider">
                                    <div id="map_slider"></div>
                                </div>
                                <input type="number" id="map_max_range" class="range-max-css" value="" step="1" min="{{ $filter_options['min_map'] }}" max="{{ $filter_options['max_map'] }}" data-max="{{ $filter_options['max_map'] }}">
                            </div>
                            
                        </div> -->
                        <div class="row">
                            <!-- <div class="col-md-3"> 
                                <label>{{ trans('inventorylabel.filters.inventory') }}</label>
                                <input type="number" id="inv_min_range" class="range-min-css" value="" step="1" min="{{ $filter_options['min_invtr'] }}" max="{{ $filter_options['max_invtr'] }}" data-min="{{ $filter_options['min_invtr'] }}">
                                <input type="number" id="inv_min_range" class="range-min-css" value="" step="1" min="0" max="{{ $filter_options['max_invtr'] }}" data-min="0">
                                <div class="slider">
                                    <div id="inv_slider"></div>
                                </div>
                                <input type="number" id="inv_max_range" class="range-max-css" value="" step="1" min="{{ $filter_options['min_invtr'] }}" max="{{ $filter_options['max_invtr'] }}" data-max="{{ $filter_options['max_invtr'] }}">
                            </div> -->
                            		
                        </div>
                        <hr />
                    </div>
                </form>

                @include('Inventory::inventoryupdate-popup')

<!--                 <div class="modal modal-scroll fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">{{ trans('inventorylabel.filters.pop_up_title') }}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                {{ Form::open(array('url' => '/inventory/downloadTemplate', 'id' => 'downloadexcel-mapping'))}}
                                                

                                            </div>
                                            <div id='filters'>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                    <select name="warehousenamess" id="warehousenamess" class="form-control" placeholder="{{ trans('inventorylabel.filters.dc') }}" required>
                                        <option value="">{{ trans('inventorylabel.filters.pop_up_select') }}</option>
                                        @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                        <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                      <button type="submit" class="btn green-meadow" id="download-excel">{{ trans('inventorylabel.filters.pop_up_download_excel_btn') }}</button>  
                                                    </div>
                                                   <div class="col-md-2"><button type="reset" id="reset-button" class="btn green-meadow" title="Reset Form"><i class="fa fa-undo "></i></button></div>
                                                </div>
                                               
                                                    
                                                   

                                            </div>
                                            {{ Form::close() }}
                                            {{ Form::open(['id' => 'uploadexcel']) }}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">






                                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                            <div>
                                                                <span class="btn default btn-file btn green-meadow btnwidth">
                                                                    <span class="fileinput-new">{{ trans('inventorylabel.filters.pop_up_choosefile') }}</span>
                                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Choose Inventory File</span>
                                                                    <input type="file" name="upload_taxfile" id="upload_taxfile" value="" class="form-control"/>
                                                                </span>
                                                                <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" >
                                                    <div class="form-group">
                                                        <label class="control-label"> </label>
                                                        <button type="button"  class="btn green-meadow" id="excel-upload-button">{{ trans('inventorylabel.filters.pop_up_upload_btn') }}</button>
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
 -->



                <div class="table">
                    <table class="inv-title">
                        <tr>
                            <th>{{ trans('zeroSOHLabels.table_header') }}</th>
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
  .ui-iggrid .ui-widget-content {
  
    border-spacing: 2px !important;
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
        text-align: right ;
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
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<!-- Inventory Grid Component  -->
<script src="{{ URL::asset('assets/admin/pages/scripts/InventoryModule/inventoryGridZeroReport.js') }}" type="text/javascript"></script>
@extends('layouts.footer')
<script type="text/javascript">
    $(document).ready(function () {
        window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
        window.groups_eg_g = $('.groups_eg_g').SumoSelect({search: true});
        
        $("#toggleFilter").click(function () {
            $("#filters").toggle("fast", function () {});
        });
    });
    
   /* //Freshness Range Slider Start
    var r_fresh_min = document.getElementById('fresh_min_range'),
        fresh_slider = document.getElementById('fresh_slider'),
        r_fresh_max = document.getElementById('fresh_max_range');

    noUiSlider.create(fresh_slider, {
        start: [0, 100],
        connect: true,
        range: {
            'min': 0,
            'max': 100
        }
    });

    fresh_slider.noUiSlider.on('update', function (values, handle) {
        var value = values[handle];
        if (handle) {
            r_fresh_max.value = Math.round(value);
        } else {
            r_fresh_min.value = Math.round(value);
        }
    });

    r_fresh_min.addEventListener('change', function () {
        fresh_slider.noUiSlider.set([this.value, null]);
    });

    r_fresh_max.addEventListener('change', function () {
        fresh_slider.noUiSlider.set([null, this.value]);
    });
    //Freshness Range Slider End
    
    //MRP Range Slider Start
    var r_mrp_min = document.getElementById('mrp_min_range'),
        mrp_slider = document.getElementById('mrp_slider'),
        r_mrp_max = document.getElementById('mrp_max_range'),
        mrp_min = Math.round($('#mrp_min_range').data('min')),
        mrp_max = Math.round($('#mrp_max_range').data('max'));

        if(mrp_max === 0){
            mrp_max = 1000;
        }

    noUiSlider.create(mrp_slider, {
        start: [mrp_min, mrp_max],
        connect: true,
        range: {
            'min': mrp_min,
            'max': mrp_max
        }
    });

    mrp_slider.noUiSlider.on('update', function (values, handle) {
        var value = values[handle];
        if (handle) {
            r_mrp_max.value = Math.round(value);
        } else {
            r_mrp_min.value = Math.round(value);
        }
    });

    r_mrp_min.addEventListener('change', function () {
        mrp_slider.noUiSlider.set([this.value, null]);
    });

    r_mrp_max.addEventListener('change', function () {
        mrp_slider.noUiSlider.set([null, this.value]);
    });
    //MRP Range Slider End
    
    //SOH Range Slider Start
    var r_soh_min = document.getElementById('soh_min_range'),
        soh_slider = document.getElementById('soh_slider'),
        r_soh_max = document.getElementById('soh_max_range'),
        soh_min = Math.round($('#soh_min_range').data('min')),
        soh_max = Math.round($('#soh_max_range').data('max'));

        if(soh_max === 0){
            soh_max = 1000;
        }

    noUiSlider.create(soh_slider, {
        start: [soh_min, soh_max],
        connect: true,
        range: {
            'min': soh_min,
            'max': soh_max
        }
    });

    soh_slider.noUiSlider.on('update', function (values, handle) {
        var value = values[handle];
        if (handle) {
            r_soh_max.value = Math.round(value);
        } else {
            r_soh_min.value = Math.round(value);
        }
    });

    r_soh_min.addEventListener('change', function () {
        soh_slider.noUiSlider.set([this.value, null]);
    });

    r_soh_max.addEventListener('change', function () {
        soh_slider.noUiSlider.set([null, this.value]);
    });
    //SOH Range Slider End
    
    //MAP Range Slider Start
    var r_map_min = document.getElementById('map_min_range'),
        map_slider = document.getElementById('map_slider'),
        r_map_max = document.getElementById('map_max_range'),
        map_min = Math.round($('#map_min_range').data('min')),
        map_max = Math.round($('#map_max_range').data('max'));

        if(map_max === 0){
            map_max = 1000;
        }

    noUiSlider.create(map_slider, {
        start: [map_min, map_max],
        connect: true,
        range: {
            'min': map_min,
            'max': map_max
        }
    });

    map_slider.noUiSlider.on('update', function (values, handle) {
        var value = values[handle];
        if (handle) {
            r_map_max.value = Math.round(value);
        } else {
            r_map_min.value = Math.round(value);
        }
    });

    r_map_min.addEventListener('change', function () {
        map_slider.noUiSlider.set([this.value, null]);
    });

    r_map_max.addEventListener('change', function () {
        map_slider.noUiSlider.set([null, this.value]);
    });
    //MAP Range Slider End
    
    //Inventory Range Slider Start
    var r_inv_min = document.getElementById('inv_min_range'),
        inv_slider = document.getElementById('inv_slider'),
        r_inv_max = document.getElementById('inv_max_range'),
        inv_min = Math.round($('#inv_min_range').data('min')),
        inv_max = Math.round($('#inv_max_range').data('max'));

        if(inv_max === 0){
            inv_max = 1000;
        }

    noUiSlider.create(inv_slider, {
        start: [inv_min, inv_max],
        connect: true,
        range: {
            'min': inv_min,
            'max': inv_max
        }
    });

    inv_slider.noUiSlider.on('update', function (values, handle) {
        var value = values[handle];
        if (handle) {
            r_inv_max.value = Math.round(value);
        } else {
            r_inv_min.value = Math.round(value);
        }
    });

    r_inv_min.addEventListener('change', function () {
        inv_slider.noUiSlider.set([this.value, null]);
    });

    r_inv_max.addEventListener('change', function () {
        inv_slider.noUiSlider.set([null, this.value]);
    });*/
    //Inventory Range Slider End
    $(function () {
        inventoryGrid();  //This is function is having the Total inventory Grid
    });
    
    
    function filterGrid() {
        console.log("filter one");
        // alert($("#product_titles").val());
        var slu_val = $("#shelf_life_uom").val(),
                sl_val = $("#shelf_life").val();
        if ((slu_val === 'nodata') && (sl_val !== '')) {
            $("#error_message").html('<div class="flash-message"><div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert"></button>Please Select Both Shlef Life & Shelf Life UOM</div></div>');
            $(".alert-warning").fadeOut(15000);
            return false;
        }
        if ((slu_val !== 'nodata') && (sl_val === '')) {
            $("#error_message").html('<div class="flash-message"><div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert"></button>Please Select Both Shlef Life & Shelf Life UOM</div></div>');
            $(".alert-warning").fadeOut(15000);
            return false;
        }

        $("#filters").toggle("fast", function () {});

        var mainFilters = [], eachFilter = {}, dcs = {}, mf = {}, brand = {}, category = {}, kvi = {}, ean_upc = {}, product_titles={}, shelf_life_uom = {}, product_char = {}, product_form = {}, sku = {};

        /*$("#dc_name option:selected").each(function (i) {
            if ($(this).length) {
                dcs[i] = $(this).val();
            }
        });
        eachFilter["dc_name"] = dcs;
*/
        $("#manf_name option:selected").each(function (i) {
            if ($(this).length) {
                mf[i] = $(this).val();
            }
        });
        eachFilter["manf_name"] = mf;

        $("#brand option:selected").each(function (i) {
            if ($(this).length) {
                brand[i] = $(this).val();
            }
        });
        eachFilter["brand"] = brand;

        $("#category option:selected").each(function (i) {
            if ($(this).length) {
                category[i] = $(this).val();
            }
        });
        eachFilter["category"] = category;
/*
        $("#kvi option:selected").each(function (i) {
            if ($(this).length) {
                kvi[i] = $(this).val();
            }
        });
        eachFilter["kvi"] = kvi;

        $("#ean_upc option:selected").each(function (i) {
            if ($(this).length) {
                ean_upc[i] = $(this).val();
            }
        });
        eachFilter["ean_upc"] = ean_upc;

        $("#product_titles option:selected").each(function (i) {
            if ($(this).length) {
                product_titles[i] = $(this).val();
            }
        });
        eachFilter["product_titles"] = product_titles;*/

        eachFilter["shelf_life"] = $("#shelf_life").val();

        $("#shelf_life_uom option:selected").each(function (i) {
            if ($(this).length) {
                shelf_life_uom[i] = $(this).val();
            }
        });
        eachFilter["shelf_life_uom"] = shelf_life_uom;

        $("#product_char option:selected").each(function (i) {
            if ($(this).length) {
                product_char[i] = $(this).val();
            }
        });
        eachFilter["product_char"] = product_char;

        $("#product_form option:selected").each(function (i) {
            if ($(this).length) {
                product_form[i] = $(this).val();
            }
        });
        eachFilter["product_form"] = product_form;

       /* $("#sku option:selected").each(function (i) {
            if ($(this).length) {
                sku[i] = $(this).val();
            }
        });
        eachFilter["sku"] = sku;*/
        /*
        eachFilter["mrp_min"] = $("#mrp_min_range").val();
        eachFilter["mrp_max"] = $("#mrp_max_range").val();
        eachFilter["soh_min"] = $("#soh_min_range").val();
        eachFilter["soh_max"] = $("#soh_max_range").val();
        eachFilter["map_min"] = $("#map_min_range").val();
        eachFilter["map_max"] = $("#map_max_range").val();
        eachFilter["inv_min"] = $("#inv_min_range").val();
        eachFilter["inv_max"] = $("#inv_max_range").val();*/
        mainFilters.push(JSON.stringify(eachFilter));

        var filterURL = "/inventory/totalinventoryZeroReport?filterData=" + mainFilters;
        $("#toggleFilter_export").attr("href", "getExportZeroReport?filterData=" + mainFilters);
        $("#inventorygrid").igGrid({dataSource: filterURL});
        console.log("filters two"+mainFilters);
        // resetFilters();
    }

    function resetFilters() {
        var dc_name = [], kvi = [], product_char = [], product_form = [], manf_name = [], brand = [], category = [], ean_upc = [],product_titles=[], sku = [];

        $("#toggleFilter_export").attr("href", "getExportZeroReport");

       /* $('#dc_name option:selected').each(function () {
            dc_name.push($(this).index());
        });
        for (var i = 0; i < dc_name.length; i++) {
            $('.dc_reset')[0].sumo.unSelectItem(dc_name[i]);
        }*/

        /*$('#kvi option:selected').each(function () {
            kvi.push($(this).index());
        });
        for (var i = 0; i < kvi.length; i++) {
            $('.kvi_reset')[0].sumo.unSelectItem(kvi[i]);
        }*/

        $('#shelf_life').val('');
        var slu_val = $("#shelf_life_uom").val();
        if (slu_val !== 'nodata') {
            $('#shelf_life_uom')[0].sumo.unSelectAll();
        }

        $('#product_char option:selected').each(function () {
            product_char.push($(this).index());
        });
        for (var i = 0; i < product_char.length; i++) {
            $('.product_char_reset')[0].sumo.unSelectItem(product_char[i]);
        }

        $('#product_form option:selected').each(function () {
            product_form.push($(this).index());
        });
        for (var i = 0; i < product_form.length; i++) {
            $('.product_form_reset')[0].sumo.unSelectItem(product_form[i]);
        }

        $('#manf_name option:selected').each(function () {
            manf_name.push($(this).index());
        });
        for (var i = 0; i < manf_name.length; i++) {
            $('.manf_name_reset')[0].sumo.unSelectItem(manf_name[i]);
        }

        $('#brand option:selected').each(function () {
            brand.push($(this).index());
        });
        for (var i = 0; i < brand.length; i++) {
            $('.brand_reset')[0].sumo.unSelectItem(brand[i]);
        }

        $('#category option:selected').each(function () {
            category.push($(this).index());
        });
        for (var i = 0; i < category.length; i++) {
            $('.category_reset')[0].sumo.unSelectItem(category[i]);
        }

/*        $('#ean_upc option:selected').each(function () {
            ean_upc.push($(this).index());
        });
        for (var i = 0; i < ean_upc.length; i++) {
            $('.ean_upc_reset')[0].sumo.unSelectItem(ean_upc[i]);
        }

        $('#product_titles option:selected').each(function () {
            product_titles.push($(this).index());
        });
        for (var i = 0; i < product_titles.length; i++) {
            $('.product_titles_reset')[0].sumo.unSelectItem(product_titles[i]);
        }

        $('#sku option:selected').each(function () {
            sku.push($(this).index());
        });
        for (var i = 0; i < sku.length; i++) {
            $('.sku_reset')[0].sumo.unSelectItem(sku[i]);
        }*/

        /*r_fresh_min.value = 0;
        r_fresh_max.value = 100;
        fresh_slider.noUiSlider.set([0, 100]);
        r_mrp_min.value = mrp_min;
        r_mrp_max.value = mrp_max;
        mrp_slider.noUiSlider.set([mrp_min, mrp_max]);
        r_soh_min.value = soh_min;
        r_soh_max.value = soh_max;
        soh_slider.noUiSlider.set([soh_min, soh_max]);
        r_map_min.value = map_min;
        r_map_max.value = map_max;
        map_slider.noUiSlider.set([map_min, map_max]);
        r_inv_min.value = inv_min;
        r_inv_max.value = inv_max;
        inv_slider.noUiSlider.set([inv_min, inv_max]);*/
    }

    function resetGrid() {
        var mainURL = "/inventory/totalinventoryZeroReport";
        $("#inventorygrid").igGrid({dataSource: mainURL});
    }


$('#upload-document').on('hidden.bs.modal', function (e) {
    console.log("Modal box closed");
    $("#warehousenamess").prop('selectedIndex',0);
});


$('#downloadexcel-mapping').submit(function (e) {
    this.submit(); // use the native submit method of the form element
    $('#upload-document').modal('toggle');
});



    
</script>

@stop
@extends('layouts.footer')