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
                <div class="caption">{{ trans('inventorylabel.filters.heading_1') }}</div>
                <input type="hidden" name="filtered_data_export" id="filtered_data_export">
                <div class="actions">
                    @if(isset($stockistab) && $stockistab==1)    
                       <a class="btn green-meadow" data-toggle="modal" data-target="#stockist_ledger" id="ledgerstockist">Stockist Ledger Export</a> <span data-placement="top"></span> 
                       @endif  
                    <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document-replanishment" class="btn green-meadow">{{ trans('inventorylabel.filters.export_replanishment') }}</a>  
                    @if($import_access == '1') 
                    <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document" class="btn green-meadow">{{ trans('inventorylabel.filters.inventory_import_btn') }}</a>    
                    @endif                    
                @if($role_access == '1')
                <!-- testing -->
                <!-- <span data-original-title="Export to Excel" data-placement="top" class="tooltips"><a href="{{ URL('inventory/getExport') }}" id="toggleFilter_export"><i class="fa fa-file-excel-o fa-lg" id="export_excel"></i></a></span> -->
                <a href="#" data-id="#" data-toggle="modal" data-target="#download-Doc-withalldata" class="btn green-meadow" id="download_inventory">{{ trans('inventorylabel.gridLevel_3_download_btn') }}</a>
                <!-- <a href="{{ URL('inventory/getExport') }}" id="toggleFilter_export" class="btn green-meadow">{{ trans('inventorylabel.filters.export_to_excel') }}</a> -->
                @endif

               @if($invAdj_access == '1')
               
                     <a href="#" data-id="#" data-toggle="modal" data-target="#upload-inv_adjustment_document" class="btn green-meadow model_popup">{{ trans('inventorylabel.filters.inv_adjustment') }}</a>
               @endif 
                @if($soh_access == 1)
                <a href="#" data-id="#" data-toggle="modal" data-target="#upload-soh-document" class="btn green-meadow">SOH Transfer</a> 
                @endif
                
               <div class="btn-group">
                <button type="button" class="btn green-meadow">Reports</button>&nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn green-meadow dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-angle-down"></i></button>
                <ul class="dropdown-menu reportsmarg" role="menu">
                <li>
                    <a href="#inventory" data-toggle="modal" class="btn green-meadow" id="inv_snapshot_id">Inv Snapshot</a>
                </li>
                <li>
                    <a href="#inventory_new" data-toggle="modal" class="btn green-meadow" id="inv_new_snapshot_id">Inv Opening/Closing Report</a>
                </li>
                <li>
                    <a href="#cyclecount" data-toggle="modal" class="btn green-meadow" id="cyclecount_id">Cycle Count</a>
                </li>
                <li>
                    <a href="#inv_summ" data-toggle="modal" class="btn green-meadow" id="inv_summ_id">Inv Summary</a>
                </li>
                </ul>
                </div>
                <a href="javascript:void(0);" id="toggleFilter"><i class="fa fa-filter fa-lg"></i></a>

                </div>
                
            </div>
            <div class="portlet-body">
                <form id="inventory_filters" action="" method="post">	
                    <div id="filters" style="display:none;">
                        <div class="row">
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="dc_name" id="dc_name" class="form-control multi-select-box dc_reset" placeholder="{{ trans('inventorylabel.filters.dc') }}">
                                        @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                    <option value="{{ $dc_id }}" <?php if ($dc_name == "DC03 Uppal") echo "selected='selected'";?>>{{ $dc_name }}</option>                                        
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="manf_name" id="manf_name" class="form-control multi-select-search-box manf_name_reset" multiple="multiple" placeholder="{{ trans('inventorylabel.filters.manufacturer') }}">
                                        @foreach ($filter_options['manfacturer_name'] as $manf_id => $manf_name)
                                        <option value="{{ $manf_id }}">{{ $manf_name }}</option>
                                        @endforeach
                                    </select>
                                </div>                        
                            </div>
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="category" id="category" class="form-control multi-select-search-box category_reset" multiple="multiple" placeholder="{{ trans('inventorylabel.filters.category') }}">
                                        {!!html_entity_decode($filter_options['category_name'])!!}
                                    </select>
                                </div>                        
                            </div>
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="brand" id="brand" class="form-control multi-select-search-box brand_reset" multiple="multiple" placeholder="{{ trans('inventorylabel.filters.brand') }}">
                                        {!!html_entity_decode($filter_options['brand_name'])!!}
                                    </select>
                                </div>                        
                            </div>
                        </div>
                        <div class="row">
                            
                        </div>
                        <div class="row">
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="product_char" id="product_char" class="form-control multi-select-box product_char_reset" multiple="multiple" placeholder="{{ trans('inventorylabel.filters.characteristics') }}">
                                        <option value="perishable">Perishable</option>
                                        <option value="flammable">Flammable</option>
                                        <option value="hazardous">Hazardous</option>
                                        <option value="odour">Odour</option>
                                        <option value="fragile">Fragile</option>
                                    </select>
                                </div>                        
                            </div>

                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <select name="product_form" id="product_form" class="form-control multi-select-box product_form_reset" multiple="multiple" placeholder="{{ trans('inventorylabel.filters.form') }}">
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
                                            <input type="number" min ="0" name="shelf_life" id="shelf_life" class="form-control" placeholder="{{ trans('inventorylabel.filters.shelf') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select name="shelf_life_uom" id="shelf_life_uom" class="form-control multi-select-box" placeholder="{{ trans('inventorylabel.filters.shelf_uom') }}">
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
                                    <input name="inv_age" id="inv_age" class="form-control" placeholder="{{ trans('inventorylabel.filters.aging') }}" />
                                </div>                        
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-3"> 
                                <label><b>{{ trans('inventorylabel.filters.fresh') }}</b></label>
                                <input type="number" id="fresh_min_range" class="range-min-css" value="" step="1" min="0" max="100">
                                <div class="slider">
                                    <div id="fresh_slider"></div>
                                </div>
                                <input type="number" id="fresh_max_range" class="range-max-css" value="" step="1" min="0" max="100">
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

                            <div class="col-md-3">
                    <b>{{ trans('inventorylabel.filters.sellable_checkBox') }}</b>  <select id="is_sellable" name="is_sellable" class="form-control">
                        <option value="">{{ trans('inventorylabel.filters.select_box_firt_option') }}</option>
                        <option value='1'>{{ trans('inventorylabel.filters.select_box_options_yes') }}</option>
                        <option value='0'>{{ trans('inventorylabel.filters.select_box_option_no') }}</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <b>{{ trans('inventorylabel.filters.enabled_checkbox') }}</b>  <select id="cp_enabled" name="cp_enabled" class="form-control">
                        <option value="">{{ trans('inventorylabel.filters.select_box_firt_option') }}</option>
                        <option value='1'>{{ trans('inventorylabel.filters.select_box_options_yes') }}</option>
                        <option value='0'>{{ trans('inventorylabel.filters.select_box_option_no') }}</option>
                    </select>
                </div>

                            
                            
                        </div>
                        <div class="row">
                            
                            

                <!-- <div class="col-md-3"> 
                 <div class="mt-checkbox-inline">
                                        <label class="label1">
                                            <input type="checkbox" name="is_sellable" id="is_sellable" value="option1"> Sellable
                                            <span></span>
                                        </label>
                                        <label class="label1">
                                            <input type="checkbox" name="cp_enabled" id="cp_enabled" value="option2"> CP Enabled
                                            <span></span>
                                        </label>
                                   
                                    </div>  
                </div> -->

                            <div class="col-md-9 text-right" style="margin-top:20px;">
                                <input type="button" value="{{ trans('inventorylabel.filters.button1') }}" class="btn btn-success" onclick="filterGridInventory();">
                                <input type="button" value="{{ trans('inventorylabel.filters.button2') }}" class="btn btn-success" onclick="resetFilters(); resetGrid();">
                            </div>		
                        </div>
                        <hr />
                    </div>
                </form>
                @include('Inventory::inventoryupdate-popup')

                                <div class="modal modal-scroll fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

                

                            <div class="modal modal-scroll fade" id="upload-document-replanishment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                                                            {{ Form::open(array('url' => '/inventory/replanishmentdownloadtemplate', 'id' => 'downloadexcel-mapping-replanishment'))}}
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
                                                                        <select name="warehousenamess-replanishment" id="warehousenamess-replanishment" class="form-control select2me" placeholder="{{ trans('inventorylabel.filters.dc') }}" required>
                                                                        <option value="">{{ trans('inventorylabel.filters.pop_up_select') }}</option>
                                                                        @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                                                            <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                                                        @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                  <button type="submit" class="btn green-meadow" id="download-excel">{{ trans('inventorylabel.filters.pop_up_download_excel_btn_replanishment') }}</button>  
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
                                                                                <span class="fileinput-new">{{ trans('inventorylabel.filters.pop_up_choosefile') }}</span>
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
                                                                    <button type="button"  class="btn green-meadow" id="excel-upload-button-replanishment">{{ trans('inventorylabel.filters.pop_up_upload_btn') }}</button>
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
            <!-- inventory adjustment -->
                    <div class="modal modal-scroll fade" id="upload-inv_adjustment_document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">{{ trans('inventorylabel.filters.inv_adjustment') }}</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet box">
                                                <div class="portlet-body">
                                                    {{ Form::open(array('url' => '/inventory/invadjustmentdownloadTemplate', 'id' => 'downloadexcel-mapping'))}}
                                                </div>
                                                <div id='filters'>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                            <select name="warehousenamess" id="warehousenamess" class="form-control select2me" placeholder="{{ trans('inventorylabel.filters.dc') }}" required>
                                                                <option value="">{{ trans('inventorylabel.filters.pop_up_select') }}</option>
                                                                @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                                                <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                          <button type="submit" class="btn green-meadow" id="download-excel">{{ trans('inventorylabel.filters.pop_up_inv_adj_download_btn') }}</button>  
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
                                                                        <span class="fileinput-new">{{ trans('inventorylabel.filters.pop_up_inv_adj_choosefile') }}</span>
                                                                        <span class="fileinput-exists" style="margin-top:-9px !important;">Choose Inventory File</span>
                                                                        <input type="file" name="upload_inv_adj_file" id="upload_inv_adj_file" value="" class="form-control"/>
                                                                    </span>
                                                                    <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" >
                                                        <div class="form-group">
                                                            <label class="control-label"> </label>
                                                            <button type="button"  class="btn green-meadow" id="inv_adj_excel-upload-button">{{ trans('inventorylabel.filters.pop_up_inv_adj_upload_btn') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                        <span id="loader" class="loader" style="display:none;">Please Wait<img src="/img/spinner.gif" style="width:225px; padding-left:20px;" /></span>
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

                

                    
            <!-- end inventory adjustment -->
<!-- rupee symboladded -->
                <div class="row">
                    <div class="col-md-12 text-right">
                        <p class="notific">* <b>All Amounts in</b> <i class="fa fa-inr"></i></p>
                    </div>  
                </div> 
<!-- Inventory Grid starts Starts -->
                <div class="modal modal-scroll fade" id="download-Doc-withalldata" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document" style="width:60%">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">{{ trans('inventorylabel.gridLevel_3_download_btn') }}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                <?php  $url = '?filterData={"freebeedata":"NOF001"}'; ?>
                                    <div class="col-md-12">
                                        
                                                {{ Form::open(array('url' => '/inventory/getExportInventory'.$url,  'id' => 'download-freebee-data', 'method'=>'POST'))}}
                                                

                                            </div>
                                            <div id='filters'>
                                                 <div class="col-md-4">
                                                        <select name="dc_names" id="dc_names" class=" select2me form-control dc_names_select" >
                                                          
                                                        </select>
                                                    </div>
                                                <div class="row">


<div class="col-md-3 text-center" style="display:none"><input type="checkbox" name="freebeechkbox" id="freebeechkbox" value = "yesfreebee" > {{ trans('inventorylabel.gridLevel_3_popup_freebeetext') }}</div>


                                                   

                                                        
                                                    </div>
                                                    <!-- <div class="row"> -->
<div class="col-md-3 text-center butmargtop">
                                                    <a href="{{ URL('inventory/getExportInventory'.$url) }}" id="toggleFilter_export"></a>
</div>
                                                    <!-- </div> -->
                                                    
                                                    <!-- <div class="col-md-4">
                                                      <button type="submit" class="btn green-meadow" id="download-excel">{{ trans('inventorylabel.filters.pop_up_download_excel_btn_replanishment') }}</button>  
                                                    </div> -->

                        <div class="col-md-3">
                           <button type="submit" class="btn green-meadow" id="inventory-upload-button" >{{ trans('inventorylabel.gridLevel_3_download_btn') }}</button>
                        </div>
                        
<!--             onclick="downloadReport()"
 -->                                            {{ Form::close() }}
                        <div class="col-md-3">
                            <button style="margin-left: 30px"  class="btn green-meadow" id="getmail" >Get All Inventory by Mail</button>
                        </div>

                                            <!-- {{ Form::open(['id' => 'replanishment-upload-excel']) }}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">






                                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                            <div>
                                                                <span class="btn default btn-file btn green-meadow btnwidth">
                                                                    <span class="fileinput-new">{{ trans('inventorylabel.filters.pop_up_choosefile') }}</span>
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
                                                        <button type="button"  class="btn green-meadow" id="excel-upload-button-replanishment">{{ trans('inventorylabel.filters.pop_up_upload_btn') }}</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <span id="loader" style="display:none;">Please Wait<img src="/img/spinner.gif" style="width:225px; padding-left:20px;" /></span>
                                                </div>      
                                            </div>   
                                            {{ Form::close() }}  -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="table-scrollable">
                    <table class="inv-title" id="inventorygrid"></table>
                </div>


                 <div class="modal modal-scroll fade" id="upload-soh-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel"><b>Soh Transfer</b></h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="portlet box">
                                                            <div class="portlet-body">
                                                                {{ Form::open(array('url' => '/inventory/downloadSohTemplate', 'id' => 'downloadexcel-SOH'))}}
                                                            </div>
                                                            <div id='filters'>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                    
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                      <button type="submit" class="btn green-meadow" id="download-excel">Download soh</button>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp  
                                                                    </div>
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
                                                                                    <input type="file" name="upload_stocktransfer_file" id="upload_stocktransfer_file" value="" class="form-control"/>
                                                                                </span>
                                                                                <span class="fileinput-filename">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6" >
                                                                    <div class="form-group">
                                                                        <label class="control-label"> </label>
                                                                        <button type="button"  class="btn green-meadow" id="import_stocktransfer_button">Upload SOH</button>
                                                                    </div>
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

<!-- Inventory Grid ends -->

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="stockist_ledger" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Stockist Ledger Report</h4>
                    </div>


                    <div class="modal-body" id="stockist_ledger">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                   {{ Form::open(array('url' => '/inventory/stockistledgerexport', 'id' => 'stockistledgerexport', 'method'=>'POST'))}}
                                           <div class="row">

                            <div class="col-md-4">
                              <div class="form-group">
                                                
                                                <select class="form-control select2me" id="warehousebanner" name="warehouse"  autocomplete="Off" placeholder="DC">
                                                    @foreach($dcs as $dc)
                                                    @if($dc->lp_wh_name!='')
                                                    <option value="{{ $dc->le_wh_id}}"> {{ $dc->lp_wh_name }}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>   
                             </div>                
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="fromdate" name="fromdate" class="form-control" placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="todate" name="todate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                                        
                                            <div class="row">
                                             <div class="col-md-12 text-center">
                                                <div class="form-group">
                                                    <button type="submit"  class="btn green-meadow" id="inventory_stockist">Submit</button>
                                                </div>
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
@include('Inventory::inventoryPopup')
@include('Inventory::snapshotPopup')
@include('Inventory::cyclecountPopup')
@include('Inventory::inventorySummary')

@stop

@section('userscript')
<style type="text/css">
.butmargtop{ margin-top:15px;}
.notific{font-size: 11px; color:f00;}

.right-align-labels{
    position: absolute;
    right: 17px;
    bottom: 50px;
    color: blue;
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
        text-align:right !important;
    }

    .mapalign {
        text-align:right !important;
    }

    .headerright {text-align: right !important;}
    /*NO need*/
    /*table[data-childgrid="true"] th.ui-iggrid-header{
        text-align: left !important;
    }
    */

    th.ui-iggrid-header:nth-child(3), th.ui-iggrid-header:nth-child(5), th.ui-iggrid-header:nth-child(7), th.ui-iggrid-header:nth-child(9), th.ui-iggrid-header:nth-child(10), th.ui-iggrid-header:nth-child(11), th.ui-iggrid-header:nth-child(12), th.ui-iggrid-header:nth-child(13), th.ui-iggrid-header:nth-child(14), th.ui-iggrid-header:nth-child(16), th.ui-iggrid-header:nth-child(17),th.ui-iggrid-header:nth-child(18), th.ui-iggrid-header:nth-child(19), th.ui-iggrid-header:nth-child(20){
        text-align: right !important;
    }

    .ui-iggrid-tablebody td:nth-child(3)
    {
        text-align: right !important;
    }
    
    #inventorygrid_espvalue, #inventorygrid_mrpvalue{text-align: right !important;}

    .mapcntalign{padding-right: 5px !important;}

    .inv-title th{
        text-align: center; background-color: #F2F2F2; height: 40px;
    }

    .ui-iggrid-headertext{padding-right: 5px !important;}


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

    .idallign{
        margin-left:34px;
    }
#inventorygrid_product_title{
    padding-right:114px;
}
#inventorygrid_sku{
    padding-right:35px;
}
#inventorygrid_kvi{
     padding-right:28px;
}

	#inventorygrid_9_inventory_child_updating_dialog_container{position: absolute !important; top:-20px !important;}
	
#inventorygrid_9_inventory_child_updating_dialog_container{position: absolute !important; top:-20px !important;}

.dropdown>.dropdown-menu:before, .dropdown-toggle>.dropdown-menu:before, .btn-group>.dropdown-menu:before {
    right: 9px;
    left: auto;
}
.dropdown>.dropdown-menu:after, .dropdown-toggle>.dropdown-menu:after, .btn-group>.dropdown-menu:after {
    right: 10px;
    left: auto;
}
.portlet.light > .portlet-title > .actions .dropdown-menu li > a {
  color: #555;
  background:#fff;
  text-align:left;
}
#inventory-upload-button{
    margin-left:68px;
}
#inventorygrid_isd{
    text-align:right;
}
#inventorygrid_isd7{
text-align:right;
    }
#inventorygrid_isd30{
    text-align:right;
}
#inventorygrid_mi{
text-align:right;
}
#inventorygrid_di{
text-align:right;
}
#inventorygrid_ci{
    text-align:right;
}#inventorygrid_mrp{
    text-align:right;
}
.bu1{
    margin-left: 10px;
    font-size: 19px;
    color:#000000;
}
.bu2{
    margin-left: 20px;
    font-size: 18px;
    color:#1d1d1d;
}.bu3{
    margin-left: 30px;
    font-size: 16px;
    color:#3a3a3a;
}.bu4{
    margin-left: 40px;
    font-size: 14px;
    color:#535353;
}.bu5{
    margin-left: 50px;
    font-size: 13px;
    color: #6d6c6c;
}.bu6{
    margin-left: 60px;
    font-size: 11px;
    color:#868383;
}

.loader {
 position:relative;
 top:40%;
 left: 40%;
 border: 5px solid #f3f3f3;
 border-radius: 50%;
 border-top: 5px solid #d3d3d3;
 width: 50px;
 height: 50px;
 -webkit-animation: spin 2s linear infinite;
 animation: spin 2s linear infinite;
 z-index : 9999999;
}
	
</style>
<!-- datepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
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
<!--Nouislider picker CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/nouislider-new/nouislider.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<!--Nouislider picker JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/nouislider-new/nouislider.js') }}" type="text/javascript"></script>
<!--Sumoselect JavaScriptFiles-->
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<!--Ignite UI Required Combined JavaScript Files--> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<!--Bootstrap dataepicker JavaScript Files-->

 <!-- Inventory Grid Component  -->
<!-- <script src="{{ URL::asset('assets/admin/pages/scripts/InventoryModule/inventoryGrid.js') }}" type="text/javascript"></script> -->
<script src="{{ URL::asset('assets/admin/pages/scripts/InventoryModule/inventoryGridNew.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/InventoryModule/validation.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> 
@extends('layouts.footer')
<script type="text/javascript">
    $(document).ready(function () {
        $('#customDatePickerZone .input-daterange').datepicker({
                format: "mm/yyyy",
                autoclose: true,
                viewMode: "months",
                minViewMode: "months"
            });
        window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
        window.groups_eg_g = $('.groups_eg_g').SumoSelect({search: true});
        
        $("#toggleFilter").click(function () {
            $("#filters").toggle("fast", function () {});
        });
    });
    
    //Freshness Range Slider Start
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
    //var r_mrp_min = document.getElementById('mrp_min_range'),
        //mrp_slider = document.getElementById('mrp_slider'),
        //r_mrp_max = document.getElementById('mrp_max_range'),
        //mrp_min = Math.round($('#mrp_min_range').data('min')),
        //mrp_max = Math.ceil($('#mrp_max_range').data('max'));

        /*if(mrp_max === 0){
            mrp_max = 1000;
        }*/

    /*noUiSlider.create(mrp_slider, {
        start: [mrp_min, mrp_max],
        connect: true,
        range: {
            'min': mrp_min,
            'max': mrp_max
        }
    });*/

   /* mrp_slider.noUiSlider.on('update', function (values, handle) {
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
    });*/
    //MRP Range Slider End
    
    //SOH Range Slider Start
    /*var r_soh_min = document.getElementById('soh_min_range'),
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
    });*/
    //SOH Range Slider End
    
    //MAP Range Slider Start
    /*var r_map_min = document.getElementById('map_min_range'),
        map_slider = document.getElementById('map_slider'),
        r_map_max = document.getElementById('map_max_range'),
        map_min = Math.round($('#map_min_range').data('min')),
        map_max = Math.ceil($('#map_max_range').data('max'));

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
    });*/
    //MAP Range Slider End
    
    //Inventory Range Slider Start
    /*var r_inv_min = document.getElementById('inv_min_range'),
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
        inventoryGrid(0);  //This is function is having the Total inventory Grid
    });
    
    
    function filterGrid() {
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
        var Sellable = $("#is_sellable option:selected").val();
        var CPenabled = $("#cp_enabled option:selected").val();
        $("#dc_name option:selected").each(function (i) {
            if ($(this).length) {
                dcs[i] = $(this).val();
            }
        });
        eachFilter["dc_name"] = dcs;

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
        eachFilter["product_titles"] = product_titles;

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

        $("#sku option:selected").each(function (i) {
            if ($(this).length) {
                sku[i] = $(this).val();
            }
        });
        eachFilter["sku"] = sku; 
        if( Sellable != "")
        {
            eachFilter['sellable'] = Sellable;
        }

        if( CPenabled != "")
        {
            eachFilter['cpEnabled'] = CPenabled;
        }
        /*The below code Chedck boxes*/
        // if($('#is_sellable').is(":checked"))
        // {
        //     eachFilter['sellable'] = 1;
        // }else{
        //     eachFilter['sellable'] = 0;
        // }

        // if($('#cp_enabled').is(":checked"))
        // {
        //     eachFilter['cpEnabled'] = 1;
        // }else{
        //     eachFilter['cpEnabled'] = 0;
        // }
        
        eachFilter["mrp_min"] = $("#mrp_min_range").val();
        eachFilter["mrp_max"] = $("#mrp_max_range").val();
        eachFilter["soh_min"] = $("#soh_min_range").val();
        eachFilter["soh_max"] = $("#soh_max_range").val();
        eachFilter["map_min"] = $("#map_min_range").val();
        eachFilter["map_max"] = $("#map_max_range").val();
        eachFilter["inv_min"] = $("#inv_min_range").val();
        eachFilter["inv_max"] = $("#inv_max_range").val();
        if($("#freebeechkbox").is(':checked'))
        {
            eachFilter["freebeedata"] = "YESF001";
        }else{
            eachFilter["freebeedata"] = "NOF001";
        }
        mainFilters.push(JSON.stringify(eachFilter));

        var filterURL = "/inventory/totalinventory?filterData=" + mainFilters;
        $("#toggleFilter_export").attr("href", "getExport?filterData=" + mainFilters);
        $("#inventorygrid").igHierarchicalGrid({dataSource: filterURL});

        // resetFilters();
    }

    $("#freebeechkbox").change(function(){
            var url = $("#toggleFilter_export").attr('href');
            let action=$('#download-freebee-data').attr('action');
            if($(this).is(':checked')) {
                url = url.replace('NOF001', 'YESF001');
                let formurl=action.replace('NOF001','YESF001');
                $('#download-freebee-data').attr('action',formurl);

            }else{
                url = url.replace('YESF001', 'NOF001');
                let formurl=action.replace('YESF001','NOF001');
                $('#download-freebee-data').attr('action',formurl);
            }

        $("#toggleFilter_export").attr("href", url);

    });

    $('#inventory-upload-button').click(function(e){
        e.preventDefault();
        var dc=$('#dc_names').val();
        console.log(dc);
        console.log(dc);
        if(dc==" "){
            alert("Please select a dc");
        }else{
            console.log($('#download-freebee-data').attr('action'));
            let action=$('#download-freebee-data').attr('action');
            console.log(dc);
            let urldc=`,"dc_name":"${dc}"}`;
            console.log(urldc);
            let formurl=action.replace('}',urldc);
            $('#download-freebee-data').attr('action',formurl);
            console.log(action.replace('}',urldc));
            //alert($('#download-freebee-data').attr('action'));
            $('#download-freebee-data').submit();
        }       
    });

    function resetFilters() {
        var mainFilters = [], eachFilter = {}
        var dc_name = [], kvi = [], product_char = [], product_form = [], manf_name = [], brand = [], category = [], ean_upc = [],product_titles=[], sku = [];
        
        if($("#freebeechkbox").is(':checked'))
        {
            eachFilter["freebeedata"] = "YESF001";
        }else{
            eachFilter["freebeedata"] = "NOF001";
        }
        mainFilters.push(JSON.stringify(eachFilter));

        $("#toggleFilter_export").attr("href", "getExport?filterData="+mainFilters);

        $('#dc_name option:selected').each(function () {
            dc_name.push($(this).index());
        });
        for (var i = 0; i < dc_name.length; i++) {
            $('.dc_reset')[0].sumo.unSelectItem(dc_name[i]);
        }

        $('#kvi option:selected').each(function () {
            kvi.push($(this).index());
        });
        for (var i = 0; i < kvi.length; i++) {
            $('.kvi_reset')[0].sumo.unSelectItem(kvi[i]);
        }

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

        $('#ean_upc option:selected').each(function () {
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
        }

        r_fresh_min.value = 0;
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
        inv_slider.noUiSlider.set([inv_min, inv_max]);
        $('#is_sellable').prop('selectedIndex',0);
        $('#cp_enabled').prop('selectedIndex',0);
        // $('#is_sellable').removeAttr('checked');
        // $('#cp_enabled').removeAttr('checked');
    }

    function resetGrid() {
        var mainURL = "/inventory/totalinventory";
        $("#inventorygrid").igHierarchicalGrid({dataSource: mainURL});
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

    $("#excel-upload-button").on('click',function () {
    var token = $("#token_value").val();
    var stn_Doc = $("#upload_taxfile")[0].files[0];
    
    
    if (typeof stn_Doc == 'undefined')
    {
        alert("Please select file");
        return false;
    }
    var formData = new FormData();
    formData.append('upload_excel_sheet', stn_Doc);
    formData.append('test', "sample");
    var ext = stn_Doc.name.split('.').pop().toLowerCase();
        if($.inArray(ext, ['xls']) == -1) {
            alert("Please choose a valid file");
            return false;
        }
        console.log(stn_Doc);
    $.ajax({
        type: "POST",
        url: "/inventory/excelUpload?_token=" + token,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        beforeSend: function () {
            $('#loader').show();
        },
        complete: function () {
            $('#loader').hide();
        },
        success: function (data)
        {
            console.log("dataaaaa"+data);
            if(data == 0)
            {
                console.log("stop here");
                $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Excel Headers were mis-matched</div></div>');  
                $("#upload_taxfile").val("");
                $(".fileinput-filename").html("");
                $("#warehousenamess").prop('selectedIndex',0);
                $('#upload-document').modal('toggle');
                return false;
            }
            if(data.rollBack=="rollBack")
            {
             $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Something went wrong, please contact Admin</div></div>');
                 $("#upload_taxfile").val("");
                $(".fileinput-filename").html("");
                $("#warehousenamess").prop('selectedIndex',0);
                $('#upload-document').modal('toggle');   
                return false;   
            }
            /*checking here if the user is not having the access for approval work flow */
           if(data.no_permission == "No Permission")
            {
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>You are not permitted for this action</div></div>');  
            
            }else{
                var datalink = data.linkdownload;
                var LINK = "<a target='_blank' href=/" + datalink + ">View Details</a>";
                var consolidatedmsg = "{{ trans('inventorylabel.filters.soh_atp_update') }}";
                consolidatedmsg = consolidatedmsg.replace('UPDATE', data.updated_count);
                consolidatedmsg = consolidatedmsg.replace('DUPLICATE', data.dpulicate_count);
                consolidatedmsg = consolidatedmsg.replace('ERROR', data.error_count);
                consolidatedmsg = consolidatedmsg.replace('ELPESP', data.elpesp_count);
                consolidatedmsg = consolidatedmsg.replace('LINK', LINK);
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+ consolidatedmsg +'</div></div>');  
            }
            
            $("#upload_taxfile").val("");
            $(".fileinput-filename").html("");
            $("#warehousenamess").prop('selectedIndex',0);
            $('#upload-document').modal('toggle');
            // $("#inventorygrid").igGrid("dataBind");
            return false;
            
        },
        error:function(jqXHR, textStatus, errorThrown) {
              console.log(textStatus, errorThrown);
            }
    });
});


    $("#excel-upload-button-replanishment").on('click',function () {
    var token = $("#token_value").val();
    var stn_Doc = $("#upload_taxfile_replanishment")[0].files[0];
    console.log("INside replainshment console");
    
    if (typeof stn_Doc == 'undefined')
    {
        alert("Please select file");
        return false;
    }
    var formData = new FormData();
    formData.append('upload_excel_sheet', stn_Doc);
    formData.append('test', "sample");
    var ext = stn_Doc.name.split('.').pop().toLowerCase();
        if($.inArray(ext, ['xls']) == -1) {
            alert("Please choose a valid file");
            return false;
        }
        // console.log(stn_Doc);return false;
    $.ajax({
        type: "POST",
        url: "/inventory/exceluploadreplanishment?_token=" + token,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        beforeSend: function () {
            $('#loader').show();
        },
        complete: function () {
            $('#loader').hide();
        },
        success: function (data)
        {
            /*checking here if the user is not having the access for approval work flow */
           if(data.no_permission == "No Permission")
            {
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>You are not permitted for this action</div></div>');  
            
            }else{
                var datalink = data.linkdownload;
                var LINK = "<a target='_blank' href=/" + datalink + ">View Details</a>";
                var consolidatedmsg = "{{ trans('inventorylabel.filters.soh_atp_update') }}";
                consolidatedmsg = consolidatedmsg.replace('UPDATE', data.updated_count);
                consolidatedmsg = consolidatedmsg.replace('DUPLICATE', data.dpulicate_count);
                consolidatedmsg = consolidatedmsg.replace('ERROR', data.error_count);
                consolidatedmsg = consolidatedmsg.replace('LINK', LINK);
                consolidatedmsg = consolidatedmsg.replace('ELPESP', data.elpesp_count);
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

//  $('#download-excel').submit(function() {
//     // Coding
//     $('#download-excel').modal('toggle');
//     return false;
// });
$('#upload-document').on('hidden.bs.modal', function (e) {
    console.log("Modal box closed");
    $("#warehousenamess").prop('selectedIndex',0);
});

$('#upload-document-replanishment').on('hidden.bs.modal', function (e) {
    console.log("Modal box closed Replanishment");
    $("#warehousenamess-replanishment").prop('selectedIndex',0);
});

//  $('#download-excel').submit(function() {
//     // Coding
//     $('#upload-document').modal('toggle');
//     return false;
// });

$('#downloadexcel-mapping').submit(function (e) {
    this.submit(); // use the native submit method of the form element
    $('#upload-document').modal('toggle');
});


$('#downloadexcel-mapping-replanishment').submit(function (e) {
    this.submit(); // use the native submit method of the form element
    $('#upload-document-replanishment').modal('toggle');
});

$("#toggleFilter_export").click(function(){
    $('#download-Doc-withalldata').modal('toggle'); 
});

/*new changes*/

function filterGridInventory() {
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
        var Sellable = $("#is_sellable option:selected").val();
        var CPenabled = $("#cp_enabled option:selected").val();
        $("#dc_name option:selected").each(function (i) {
            if ($(this).length) {
                dcs[i] = $(this).val();
            }
        });
        eachFilter["dc_name"] = dcs;

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

        /*$("#kvi option:selected").each(function (i) {
            if ($(this).length) {
                kvi[i] = $(this).val();
            }
        });
        eachFilter["kvi"] = kvi;*/

        $("#ean_upc option:selected").each(function (i) {
            if ($(this).length) {
                ean_upc[i] = $(this).val();
            }
        });
        eachFilter["ean_upc"] = ean_upc;

        /*$("#product_titles option:selected").each(function (i) {
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

        /*$("#sku option:selected").each(function (i) {
            if ($(this).length) {
                sku[i] = $(this).val();
            }
        });
        eachFilter["sku"] = sku; */
        if( Sellable != "")
        {
            eachFilter['sellable'] = Sellable;
        }

        if( CPenabled != "")
        {
            eachFilter['cpEnabled'] = CPenabled;
        }
        /*The below code Chedck boxes*/
        // if($('#is_sellable').is(":checked"))
        // {
        //     eachFilter['sellable'] = 1;
        // }else{
        //     eachFilter['sellable'] = 0;
        // }

        // if($('#cp_enabled').is(":checked"))
        // {
        //     eachFilter['cpEnabled'] = 1;
        // }else{
        //     eachFilter['cpEnabled'] = 0;
        // }
        
        //eachFilter["mrp_min"] = $("#mrp_min_range").val();
        //eachFilter["mrp_max"] = $("#mrp_max_range").val();
        //eachFilter["soh_min"] = $("#soh_min_range").val();
        //eachFilter["soh_max"] = $("#soh_max_range").val();
        //eachFilter["map_min"] = $("#map_min_range").val();
        //eachFilter["map_max"] = $("#map_max_range").val();
        //eachFilter["inv_min"] = $("#inv_min_range").val();
        //eachFilter["inv_max"] = $("#inv_max_range").val();
        if($("#freebeechkbox").is(':checked'))
        {
            eachFilter["freebeedata"] = "YESF001";
        }else{
            eachFilter["freebeedata"] = "NOF001";
        }
        mainFilters.push(JSON.stringify(eachFilter));

        var filterURL = "/inventory/totalinventorygrid?filterData=" + mainFilters;
        $("#toggleFilter_export").attr("href", "getExport?filterData=" + mainFilters);
        $("#inventorygrid").igHierarchicalGrid({dataSource: filterURL});

        // resetFilters();
    }

var currDate = new Date();
var dates = $("#start_date, #end_date").datepicker({
    maxDate: currDate,
    dateFormat: 'dd-mm-yy',
    onSelect: function(date) {
        for(var i = 0; i < dates.length; ++i) {
            if(dates[i].id > this.id)
                $(dates[i]).datepicker('option', 'maxDate', date);
            else if(dates[i].id < this.id)
                $(dates[i]).datepicker('option', 'minDate', date);
        }
    } 
});

    $("#inv_snapshot_id").click(function(){
        $("#inventoryForm")[0].reset();
        $("#product_id").select2("val","");
    });
    $("#inv_new_snapshot_id").click(function(){
        $("#inventoryNewForm")[0].reset();
        $("#product_new_id").select2("val","");
    });


var presentDate = new Date();
var enddates = $("#cycleCount_stdate, #cycleCount_eddate").datepicker({
    maxDate: presentDate,
    dateFormat: 'dd-mm-yy',
    onSelect: function(date) {
        for(var i = 0; i < enddates.length; ++i) {
            if(enddates[i].id > this.id)
                $(enddates[i]).datepicker('option', 'maxDate', date);
            else if(enddates[i].id < this.id)
                $(enddates[i]).datepicker('option', 'minDate', date);
        }
    } 
});

     $("#cyclecount_id").click(function(){
        $("#cyclecountForm")[0].reset();
    }); 
        $("#inv_adj_excel-upload-button").on('click',function () {
    var token = $("#token_value").val();
    var stn_Doc = $("#upload_inv_adj_file")[0].files[0];
    
    
    if (typeof stn_Doc == 'undefined')
    {
        alert("Please select file");
        return false;
    }
    var formData = new FormData();
    formData.append('upload_excel_sheet', stn_Doc);
    formData.append('test', "sample");
    var ext = stn_Doc.name.split('.').pop().toLowerCase();
        if($.inArray(ext, ['xls']) == -1) {
            alert("Please choose a valid file");
            return false;
        }
        console.log(stn_Doc);
    $.ajax({
        type: "POST",
        url: "/inventory/InvAdjExcelImport?_token=" + token,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        beforeSend: function () {
            $('#loader').show();
        },
        complete: function () {
            $('#loader').hide();
        },
        success: function (data)
        {
            console.log("dataaaaa"+data);
            if(data == 0)
            {
                console.log("stop here");
                $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Excel Headers were mis-matched</div></div>');  
                $("#upload_taxfile").val("");
                $(".fileinput-filename").html("");
                $("#warehousenamess").prop('selectedIndex',0);
                $('#upload-inv_adjustment_document').modal('toggle');
                return false;
            }
            if(data.rollBack=="rollBack")
            {
             $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Something went wrong, please contact Admin</div></div>');
                 $("#upload_taxfile").val("");
                $(".fileinput-filename").html("");
                $("#warehousenamess").prop('selectedIndex',0);
                $('#upload-inv_adjustment_document').modal('toggle');   
                return false;   
            }
            /*checking here if the user is not having the access for approval work flow */
           if(data.no_permission == "No Permission")
            {
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>You are not permitted for this action</div></div>');  
            
            }else{
                var datalink = data.linkdownload;
                var LINK = "<a target='_blank' href=/" + datalink + ">View Details</a>";
                var consolidatedmsg = "{{ trans('inventorylabel.filters.soh_atp_update') }}";
                consolidatedmsg = consolidatedmsg.replace('UPDATE', data.updated_count);
                consolidatedmsg = consolidatedmsg.replace('DUPLICATE', data.dpulicate_count);
                consolidatedmsg = consolidatedmsg.replace('ERROR', data.error_count);
                consolidatedmsg = consolidatedmsg.replace('LINK', LINK);
                consolidatedmsg = consolidatedmsg.replace('ELPESP', data.elpesp_count);
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+ consolidatedmsg +'</div></div>');  
            }
            
            $("#upload_taxfile").val("");
            $(".fileinput-filename").html("");
            $("#warehousenamess").prop('selectedIndex',0);
            $('#upload-inv_adjustment_document').modal('toggle');
            // $("#inventorygrid").igGrid("dataBind");
            return false;
            
        },
        error:function(jqXHR, textStatus, errorThrown) {
              console.log(textStatus, errorThrown);
            }
    });
});
</script>
<script type="text/javascript">
    
    $("#inv_summ_id").click(function(){
        $("#inventorySummForm")[0].reset();
        $("#invSumProduct_id").select2("val","");
    });


    var start  = new Date();
    var end  = new Date();
    $('#sum_start_date').datepicker({
        endDate: end,
        maxDate:'0',
        autoclose: true,
        dateFormat:'dd-mm-yy',
        onSelect: function (selected) {
            $("#sum_end_date").datepicker("option", "minDate", $(this).val());
        }
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#sum_end_date').datepicker('setStartDate', stDate);
        $("#sum_end_date").datepicker("option", "minDate", stDate);
    });

    $('#sum_end_date').datepicker({
        startDate: start,
        endDate: end,
        autoclose: true,
        dateFormat:'dd-mm-yy',
    }).on('changeDate', function () {
        $('#sum_start_date').datepicker('setEndDate', new Date($(this).val()));
    });
    $("#commentForm").validate();
    $(".close").click(function(){
        $("#upload_inv_adj_file").val('');
        $(".fileinput-filename").html("");

    });
    $(".model_popup").click(function(){
        $(".fileinput-filename").html("");
        $("#upload_inv_adj_file").val("");
    });


    $(function () {
            $("#fromdate").datepicker({
                format: 'dd/mm/yyyy',//'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: "-10:+0", // last ten years
                //yearRange: new Date().getFullYear().toString() + ':' + new Date().getFullYear().toString(),
                onClose: function (selectedDate) {
                    $("#todate").datepicker("option", "minDate", selectedDate);
                }
            });
            $("#todate").datepicker({
                format: 'dd/mm/yyyy',//'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                 yearRange: "-10:+0", // last ten years
                //yearRange: new Date().getFullYear().toString() + ':' + new Date().getFullYear().toString(),
                onClose: function (selectedDate) {
                    $("#fromdate").datepicker("option", "maxDate", selectedDate);
                }
            });
        });

 $('#inventory_stockist').on('click', function() {

    var dc=$("#warehousebanner").val();

    var startDate = document.getElementById("fromdate").value;
    var endDate = document.getElementById("todate").value;

    if(startDate==""){
        alert("Please Select From Date");
        return false;
    }

    if(endDate==""){
        alert("Please Select To Date");
        return false;
    }
});

  $("#ledgerstockist").on('click', function() {

        document.getElementById("stockistledgerexport").reset();
        
    });
  $('#download_inventory').on('click',function(){
        $('#loader').show();
        var token = $("#token_value").val();
        $.ajax({
            type:'get',
            headers: {'X-CSRF-TOKEN': token},
            url:'/inventory/getbu',
            success: function(res){
                //console.log(res);
                $('#dc_names').html('');
                $('#dc_names').append('<option value=" " selected="true">Please select</option>')
                res.forEach(data=>{
                    console.log(data);
                    $('#dc_names').append(data);
                });
                $("#dc_names").select2("val", " ");
              //  $('#dc_names')[0].sumo.reload();
                $('#loader').hide();
            }

        });
  });
  $('#getmail').on('click',function(){
    var token = $("#token_value").val();

    $.ajax({
        type:'post',
        headers: {'X-CSRF-TOKEN': token},
        url :'/inventory/getExportInventory?filterData={"freebeedata":"NOF001","dc_name":"all"}',
        success:function(res){
            alert(res);
        }
    })
  })
  
</script>
@stop
@extends('layouts.footer')