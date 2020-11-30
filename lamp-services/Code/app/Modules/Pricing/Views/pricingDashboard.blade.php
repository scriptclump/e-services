@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<?php View::share('title', 'Price Manager'); ?>
<span id="success_message">@include('flash::message')</span>
<span id="success_message_ajax"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height:650px;">
            <div class="portlet-title">
                <div class="caption">
                    Price Manager Dashboard
                </div>

                 <div class="col-md-2">
                    <div class="form-group">
                        <select id = "dc_id"  name = "dc_id" class="form-control" style="margin-top: 6px;">
                            <option value = "">--Please select--</option>
                            @foreach($dcs as $alldcs)
                            <option value = "{{$alldcs->le_wh_id}}">{{$alldcs->lp_wh_name}} - ({{$alldcs->name}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="actions">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">

                    <a style="display: none;" href="#" data-id="#" data-toggle="modal" data-target="#cashback-upload-document" class="btn green-meadow">Upload Cashback Template</a>
                    @if($uploadPriceAccess==1)
                    <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document" class="btn green-meadow">Upload Pricing Template</a>
                    @endif
                    @if($addPriceAccess==1)
                    <a href="#" data-id="#" data-toggle="modal" data-target="#save_price" class="btn green-meadow">Add Price</a>
                    <span style="display:none; color:red;" id="add_price_message">Search& Select Product!</span>
                    @endif

                </div>
            </div>

            <div class="portlet-body">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Search Product</label>
                            <input type="text" class="form-control auto-comp" name="product_srch" id="product_srch" placeholder ="Product Title, SKU"/>
                            <input type="hidden" name="product_id" value="" id="product_id">
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group margtop">
                            <label class="control-label">&nbsp;</label>
                            <a href="javascript:;" class="moveright" onclick="showAdvanceFilder();">Show Advance Search</a>
                            <a href="javascript:;" class="moveLeft" onclick="  hideAdvanceFilter();" style="display: none;">Hide Advance Search</i></a>
                        </div>
                    </div>
                    <div style="display: none;" id="advanceSearch">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Search By Manufacturer</label>
                                    <select name = "manufac" id="manufac" class="form-control select2me" onChange="loadBrandId();">
                                        <option value = "">--Please select--</option>
                                    @foreach($getManufactureDetails as $manufacData)
                                        <option value = "{{$manufacData->legal_entity_id}}">{{$manufacData->business_legal_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Search By Brand</label>
                                <select id="brand" name = "brand" class="form-control select2me" onchange="loadProductAsPerBrandInModal('product_dropdown', 'brand');">
                                        <option value = "">--Please select--</option>
                                    @foreach($getBrandDetails as $brandData)
                                        <option value = "{{$brandData->brand_id}}">{{$brandData->brand_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Search By Product</label>
                                <select id = "product_dropdown"  name = "product_dropdown" class="form-control select2me">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Modal -->

                <div class="modal fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">PRICE UPLOAD</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">

                                                {{ Form::open(array('url' => '/pricing/downloadexcelforslabprice', 'id' => 'downloadexcel-slabpricing'))}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Manufacturer</label>

                                                            <select id="mdl_manufac"  name="mdl_manufac" class="form-control" onchange="loadBrandInModal();" >
                                                                    <option value = "">--Please select--</option>
                                                                @foreach($getManufactureDetails as $manufacData)
                                                                    <option value = "{{$manufacData->legal_entity_id}}">{{$manufacData->business_legal_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Brand</label>

                                                            <select id ="mdl_brand"  name ="mdl_brand" class="form-control" onchange="loadProductAsPerBrandInModal('mdl_products', 'mdl_brand');">
                                                                    <option value = "">--Please select--</option>
                                                                @foreach($getBrandDetails as $brandData)
                                                                    <option value = "{{$brandData->brand_id}}">{{$brandData->brand_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Product</label>
                                                            <input type = "text" id = "mdl_products_name"  name = "mdl_products_name" class="form-control" placeholder ="Product Title, SKU" />
                                                            <input type = "hidden" id = "mdl_products"  name = "mdl_products" class="form-control"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Category</label>

                                                            <select id = "mdl_category"  name = "mdl_category" class="form-control" >
                                                                    <option value = "all">All</option>
                                                                @foreach($getCategoryDetails as $categoryData)
                                                                    <option value = "{{$categoryData->category_id}}">{{$categoryData->cat_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">State</label>

                                                            <select id = "mdl_state"  name = "mdl_state" class="form-control" >
                                                                @foreach($getStateDetails as $stateData)
                                                                    <option value = "{{$stateData->zone_id}}">{{$stateData->name}}</option>
                                                                @endforeach
                                                            </select>
                                                             @if ($errors->has('mdl_state'))<p style="color:red;">{!!$errors->first('mdl_state')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Warehouse</label>
                                                             <select id = "upload_dc"  name = "upload_dc" class="form-control">
                                                                @foreach($dcs as $alldcs)
                                                                <option value = "{{$alldcs->le_wh_id}}">{{$alldcs->lp_wh_name}} - ({{$alldcs->name}})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="multiple" class="control-label">Customer Group</label>
                                                            <select id = "mdl_custgroup"  name = "mdl_custgroup" class="form-control" >
                                                                    <option value = "all">All</option>
                                                                @foreach($getCustomerGroup as $customerData)
                                                                    <option value = "{{$customerData->value}}">{{$customerData->master_lookup_name}}</option>
                                                                @endforeach
                                                            </select>
                                                             @if ($errors->has('mdl_custgroup'))<p style="color:red;">{!!$errors->first('mdl_custgroup')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn green-meadow" id="download-excel">Download Pricing Template</button>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                {{ Form::close() }}


                                                {{ Form::open(['id' => 'frm_price_slab']) }}
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">

                                                        <div class="form-group">
                                                            <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                                    <input type="file" name="upload_pricefile" id="upload_pricefile" value=""/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <button type="button"  class="btn green-meadow" id="price-upload-button">Upload Pricing Template</button>
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


                <div class="modal fade" id="cashback-upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Cashback Upload</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">

                                                {{ Form::open(array('url' => '/pricing/downloadexcelforcashback', 'id' => 'downloadexcel-cashback'))}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Manufacturer</label>

                                                            <select id="mdl_manufac"  name="mdl_manufac" class="form-control" onchange="loadBrandInModal();" >
                                                                    <option value = "">--Please select--</option>
                                                                @foreach($getManufactureDetails as $manufacData)
                                                                    <option value = "{{$manufacData->legal_entity_id}}">{{$manufacData->business_legal_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Brand</label>

                                                            <select id ="mdl_brand"  name ="mdl_brand" class="form-control" onchange="loadProductAsPerBrandInModal('mdl_products', 'mdl_brand');">
                                                                    <option value = "">--Please select--</option>
                                                                @foreach($getBrandDetails as $brandData)
                                                                    <option value = "{{$brandData->brand_id}}">{{$brandData->brand_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Product</label>
                                                            <input type = "text" id = "mdl_products_name"  name = "mdl_products_name" class="form-control" placeholder ="Product Title, SKU" />
                                                            <input type = "hidden" id = "mdl_products"  name = "mdl_products" class="form-control"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="product_star">Product Star</label>
                                                            <select id = "product_star"  name =  "product_star" class="form-control" >
                                                            <option value="all">All</option>
                                                            @foreach($product_stars as $product_star)
                                                                <option value = "{{$product_star->value}}">{{$product_star->master_lookup_name}}</option>
                                                            @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">State</label>

                                                            <select id = "mdl_state"  name = "mdl_state" class="form-control" >
                                                                @foreach($getStateDetails as $stateData)
                                                                    <option value = "{{$stateData->zone_id}}">{{$stateData->name}}</option>
                                                                @endforeach
                                                            </select>
                                                             @if ($errors->has('mdl_state'))<p style="color:red;">{!!$errors->first('mdl_state')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="multiple" class="control-label">Customer Group</label>

                                                            <select id = "mdl_custgroup"  name = "mdl_custgroup" class="form-control" >
                                                                    <option value = "all">All</option>
                                                                @foreach($getCustomerGroup as $customerData)
                                                                    <option value = "{{$customerData->value}}">{{$customerData->master_lookup_name}}</option>
                                                                @endforeach
                                                            </select>
                                                             @if ($errors->has('mdl_custgroup'))<p style="color:red;">{!!$errors->first('mdl_custgroup')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn green-meadow" id="download-excel">Download Cashback Template</button>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                {{ Form::close() }}


                                                {{ Form::open(['id' => 'frm_price_slab']) }}
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">

                                                        <div class="form-group">
                                                            <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                                    <input type="file" name="upload_cashbackfile" id="upload_cashbackfile" value=""/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <button type="button"  class="btn green-meadow" id="cashback-upload-button">Upload Cashback Template</button>
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
                </div><div class="modal fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">PRICE UPLOAD</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">

                                                {{ Form::open(array('url' => '/pricing/downloadexcelforslabprice', 'id' => 'downloadexcel-slabpricing'))}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Manufacturer</label>

                                                            <select id="mdl_manufac"  name="mdl_manufac" class="form-control" onchange="loadBrandInModal();" >
                                                                    <option value = "">--Please select--</option>
                                                                @foreach($getManufactureDetails as $manufacData)
                                                                    <option value = "{{$manufacData->legal_entity_id}}">{{$manufacData->business_legal_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Brand</label>

                                                            <select id ="mdl_brand"  name ="mdl_brand" class="form-control" onchange="loadProductAsPerBrandInModal('mdl_products', 'mdl_brand');">
                                                                    <option value = "">--Please select--</option>
                                                                @foreach($getBrandDetails as $brandData)
                                                                    <option value = "{{$brandData->brand_id}}">{{$brandData->brand_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Product</label>
                                                            <input type = "text" id = "mdl_products_name"  name = "mdl_products_name" class="form-control" placeholder ="Product Title, SKU" />
                                                            <input type = "hidden" id = "mdl_products"  name = "mdl_products" class="form-control"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Category</label>

                                                            <select id = "mdl_category"  name = "mdl_category" class="form-control" >
                                                                    <option value = "all">All</option>
                                                                @foreach($getCategoryDetails as $categoryData)
                                                                    <option value = "{{$categoryData->category_id}}">{{$categoryData->cat_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">State</label>

                                                            <select id = "mdl_state"  name = "mdl_state" class="form-control" >
                                                                @foreach($getStateDetails as $stateData)
                                                                    <option value = "{{$stateData->zone_id}}">{{$stateData->name}}</option>
                                                                @endforeach
                                                            </select>
                                                             @if ($errors->has('mdl_state'))<p style="color:red;">{!!$errors->first('mdl_state')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="multiple" class="control-label">Customer Group</label>

                                                            <select id = "mdl_custgroup"  name = "mdl_custgroup" class="form-control" >
                                                                    <option value = "all">All</option>
                                                                @foreach($getCustomerGroup as $customerData)
                                                                    <option value = "{{$customerData->value}}">{{$customerData->master_lookup_name}}</option>
                                                                @endforeach
                                                            </select>
                                                             @if ($errors->has('mdl_custgroup'))<p style="color:red;">{!!$errors->first('mdl_custgroup')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn green-meadow" id="download-excel">Download Pricing Template</button>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                {{ Form::close() }}


                                                {{ Form::open(['id' => 'frm_price_slab']) }}
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">

                                                        <div class="form-group">
                                                            <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                                    <input type="file" name="upload_pricefile" id="upload_pricefile" value=""/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <button type="button"  class="btn green-meadow" id="price-upload-button">Upload Pricing Template</button>
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

                <div class="addEditPriceSection">
                    @include('Pricing::addEditPriceSection')
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-scrollable">
                            <table id="priceList"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('style')
<style type="text/css">
.ui-autocomplete{z-index: 99999 !important; height: 250px !important; border:1px solid #efefef !important; overflow-y:scroll !important;overflow-x:hidden !important; width:300px !important; white-space: pre-wrap !important;}

.ui-iggrid-results{
        height: 13px !important;
        font-weight: bold;
        margin-left:524px;
    }
.modal-dialog {
    width: 1220px !important;
}
.textRightAlign {
        text-align:right !important;
    }
/*#priceList_DI{
    text-align:right;
}#priceList_MI{
    text-align:right;
}#priceList_CI{
    text-align:right;
}*/
#priceList_price{
    text-align:right;
}
/*#priceList_elp{
    text-align:right;
}*/
#priceList_ptr{
    text-align:right;
}#priceList_pager_label{
   margin-left: 50px;
</style>


@stop

@section('userscript')

<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/priceModel.js') }}" type="text/javascript"></script>
<!--Sumoselect JavaScriptFiles-->
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
@extends('layouts.footer')

<script>
    var updatePriceID = 0;
    var globalPageFlag = 1;
    $(function () {
        
        var url = window.location.href;
        var urlArr = url.split("/");
        var status  = urlArr[5];
        if(status == null)
        {
            status == "";
        }

        $("#priceList").igGrid({
            dataSource: '/pricing/pricingdata',
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            recordCountKey: 'TotalRecordsCount',
            generateCompactJSONResponse: false, 
            enableUTCDates: true, 
            width: "100%",
            height: "100%",
            columns: [
                { headerText: "Product Name", key: "product_title", dataType: "string", width: "15%" },
               { headerText: "SKU", key: "sku", dataType: "string", width: "15%" },
               { headerText: "DC", key: "DCName", dataType: "string", width: "10%" },
               { headerText: "MFG", key: "ManufacName", dataType: "string", width: "11%" },
               { headerText: "Brand", key: "BrandName", dataType: "string", width: "11%" },
               { headerText: "Category", key: "Category", dataType: "string", width: "15%" },
               { headerText: "Location", key: "StateName", dataType: "string", width: "8%" },
               { headerText: "Customer Group", key: "CustomerTypeName", dataType: "string", width: "15%" },
                { headerText: "SP", key: "price", dataType: "number", format: "0.00",width: "10%", template: "<div style='text-align:right'>${price}</div>" },
                { headerText: "PTR", key: "ptr", dataType: "number",format: "0.00", width: "10%", template: "<div style='text-align:right'>${ptr}</div>" },
                { headerText: "Effective Date", key: "effective_date", dataType: "date", format:"dd/MM/yyyy", width: "15%", template: "<div style='text-align:center'>${effective_date_grid}</div>" },
                { headerText: "CP Enabled", key: "cpEnabled", dataType: "string", width: "10%" },
                // { headerText: "LP", key: "elp", dataType: "number",format: "0.00", width: "8%",template: '<div class="textRightAlign"> ${elp} </div>' },
                // { headerText: "DI", key: "DI", dataType: "number", width: "10%",template: '<div class="textRightAlign"> ${DI} </div>' },
                // { headerText: "MI", key: "MI", dataType: "number", width: "10%",template: '<div class="textRightAlign"> ${MI} </div>' },
                // { headerText: "CI", key: "CI", dataType: "number", width: "10%",template: '<div class="textRightAlign"> ${CI} </div>' },
                { headerText: "Actions", key: "CustomAction", dataType: "string", width: "10%"},
                 ],
             features: [
                 {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'CustomAction', allowSorting: false },
                    {columnKey: 'product_title', allowSorting: true },
                    {columnKey: 'seller_sku', allowSorting: true },
                    {columnKey: 'upc', allowSorting: true },
                    {columnKey: 'StateName', allowSorting: true },
                    {columnKey: 'CustomerTypeName', allowSorting: true },
                    {columnKey: 'price', allowFiltering: true },
                    {columnKey: 'ptr', allowFiltering: true },
                    {columnKey: 'effective_date', allowSorting: true },
                    //{columnKey: 'cpEnabled', allowSorting: false },
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'product_title', allowFiltering: true },
                        {columnKey: 'seller_sku', allowFiltering: true },
                        {columnKey: 'upc', allowFiltering: true },
                        {columnKey: 'ptr', allowFiltering: true },
                        {columnKey: 'StateName', allowFiltering: true },
                        {columnKey: 'CustomAction', allowFiltering: false },
                        {columnKey: 'effective_date', allowFiltering: true },
                      //  {columnKey: 'cpEnabled', allowFiltering: false },
                        /*{columnKey: 'DI', allowFiltering: false },
                        {columnKey: 'MI', allowFiltering: false },
                        {columnKey: 'CI', allowFiltering: false },*/
                    ]
                },
                { 
                    name: 'Paging',
                    type: 'remote',
                    pageSize: 10,
                    recordCountKey: 'TotalRecordsCount',
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
 
                }
                
            ],
            primaryKey: 'product_id',
            width: '100%',
            height: '500px',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            
        });
    });  
     

    $(document).ready(function() {

        setTimeout(function() {
            $('#success_message').fadeOut('slow');
        }, 3000);  
    });

    function filterdata(){
        if( $('#product_srch').val()==''){
            $('#product_id').val('');
        }

        var product = '';
        if( $('#product_id').val()!=null ){
            product = $('#product_id').val();
        }
        var manufac = $('#manufac').val();
        var brand = $('#brand').val();

        if( $('#product_dropdown').val()!='' && $('#product_dropdown').val()!=null ){
            product = $('#product_dropdown').val();
        }

        var sortURL = "/pricing/pricingdata";
        ds = new $.ig.DataSource({
            type: "json",
            responseDataKey: "results",
            dataSource: sortURL,
            callback: function (success, error) {
                if (success) {
                    $("#priceList").igGrid({
                            dataSource: ds,
                            autoGenerateColumns: false
                    });
                } else {
                    alert(error);
                }
            },
        });

        ds.dataBind();
    } 
    
    $("#price-upload-button").click(function () {
        
        token  = $("#csrf-token").val();
        var stn_Doc = $("#upload_pricefile")[0].files[0];
        var formData = new FormData();
        formData.append('price_data', stn_Doc);
        formData.append('test', "sample");
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': token},
            url: "/pricing/uploadpriceslab",
            data: formData,
            processData: false,
            contentType: false,
            success: function (data){
                
                filterdata();
                console.log(data);
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data+'</div></div>' );
            }
        });

        $('#upload-document').modal('toggle');
        
    });
    $('#upload-document').on('hidden.bs.modal', function (e) {
        $("#upload_pricefile").val("");
    });
    //cashback upload
    $("#cashback-upload-button").click(function () {
        
        token  = $("#csrf-token").val();
        var stn_Doc = $("#upload_cashbackfile")[0].files[0];
        var formData = new FormData();
        formData.append('upload_cashbackfile', stn_Doc);
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': token},
            url: "/pricing/uploadcashbacks",
            data: formData,
            processData: false,
            contentType: false,
            success: function (data){
                
                filterdata();
                console.log(data);
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data+'</div></div>' );
                $(".alert-success").fadeOut(20000)
            }
        });

        $('#cashback-upload-document').modal('toggle');
    });
    $('#downloadexcel-slabpricing').submit(function(e) {
        e.preventDefault(); // don't submit multiple times
        this.submit(); // use the native submit method of the form element
        $('#upload-document').modal('toggle');
    });

  
    $( "#product_srch" ).autocomplete({
        minLength:2,
        source: '/pricing/getlist?manufac=&brand=',  
        select: function (event, ui) {
            var label = ui.item.label;
            var sku = ui.item.sku;
            var product_id = ui.item.product_id;
            $('#sku').val(sku);
            $('#product_id').val(product_id);
        }
    });

    //for upload modal
    $( "#mdl_products_name" ).autocomplete({
        minLength:2,
        source: function (request, response)
        {

            $.ajax(
            {

                url: '/pricing/getlist?manufac=' + $("#mdl_manufac").val() + '&brand=' + $("#mdl_brand").val(),
                dataType: "json",
                data:
                {
                    term: request.term,
                },
                success: function (data)
                {
                    response(data);
                }
            });
        },  
        select: function (event, ui) {
            var label = ui.item.label;
            var sku = ui.item.sku;
            var product_id = ui.item.product_id;
            $('#mdl_products').val(product_id);
        }
    });
 
    function showAdvanceFilder() {
        $('.moveLeft').show();
        $('.moveright').hide();
        $('#product_srch').attr("disabled", "disabled");
        $('#product_srch').val('');
        $('#product_id').val('');
        $('#advanceSearch').show();
    }

    function hideAdvanceFilter() {
        $('.moveLeft').hide();
        $('.moveright').show();
        $('#product_srch').removeAttr("disabled");
        $('#advanceSearch').hide();
    }
    
    function loadBrandId(){
        var manufac = $("#manufac").val();
        token  = $("#csrf-token").val(); 
        $('#brand').val('');
        // prepare the ajax call to get the brand information
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/pricing/getbrandsasmanufac/'+manufac,
            success: function( data ) {
                    if(data){
                        var brand = $('#brand');
                        brand.find('option').remove().end();
                        for(var i=0; i<data.length; i++){
                            brand.append(
                                $('<option></option>').val(data[i].brand_id).html(data[i].brand_name)
                            );
                        }
                    }
                    $('#brand').val('');
                }
        });
    }

    function loadBrandInModal(){
        var manufac = $("#mdl_manufac").val();
        token  = $("#csrf-token").val(); 
        $('#mdl_brand').val('');
        // prepare the ajax call to get the brand information
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/pricing/getbrandsasmanufac/'+manufac,
            success: function( data ) {
                    if(data){
                        var brand = $('#mdl_brand');
                        brand.find('option').remove().end();
                        brand.append(
                                $('<option></option>').val('all').html("All")
                            );
                        for(var i=0; i<data.length; i++){
                            brand.append(
                                $('<option></option>').val(data[i].brand_id).html(data[i].brand_name)
                            );
                        }
                    }
                    $('#mdl_brand').val('');
                }
        });
    }

    function loadProductAsPerBrandInModal(productDW, brandDW){
        var brandid = $("#"+brandDW).val();
        token  = $("#csrf-token").val(); 
        $('#'+productDW).val('');
        // prepare the ajax call to get the brand information
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/pricing/getproductasbrand/'+brandid,
            success: function( data ) {
                    if(data){
                        var brand = $('#'+productDW);
                        brand.find('option').remove().end();
                        for(var i=0; i<data.length; i++){
                            brand.append(
                                $('<option></option>').val(data[i].product_id).html(data[i].product_title)
                            );
                        }
                    }
                    $('#'+productDW).val('');
                }
        });
    }

    function savePriceDataFromPrice(){
        savePriceData();
    }

    function deleteData(priceID){
        token  = $("#csrf-token").val();
        var pricing_delete = confirm("Are you sure you want to delete this Pricing Data ?"), self = $(this);
            if ( pricing_delete == true )
            {
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                data: 'deleteData='+priceID,
                type: "POST",
                url: '/pricing/deletepricedetails',
                success: function( data ) {

                    filterdata();

                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Price Data deleted successfully</div></div>');
                $(".alert-success").fadeOut(20000)
                        
                    }
            });  
        }    
    }

    $('#priceList').on("iggriddatarendered", function (event, args) {
        $("#priceList_product_title > span.ui-iggrid-headertext").attr("title","Product Name");
        $("#priceList_sku > span.ui-iggrid-headertext").attr("title","SKU");
        $("#priceList_ManufacName > span.ui-iggrid-headertext").attr("title","Manufacturer");
        $("#priceList_BrandName > span.ui-iggrid-headertext").attr("title","Brand Name");
        $("#priceList_Category > span.ui-iggrid-headertext").attr("title","Category");
        $("#priceList_StateName > span.ui-iggrid-headertext").attr("title","State");
        $("#priceList_CustomerTypeName > span.ui-iggrid-headertext").attr("title","Customer Type");
        $("#priceList_price > span.ui-iggrid-headertext").attr("title","Selling Price");
        $("#priceList_ptr > span.ui-iggrid-headertext").attr("title","PTR");
        $("#priceList_effective_date > span.ui-iggrid-headertext").attr("title","Effective Date");
        $("#priceList_cpEnabled > span.ui-iggrid-headertext").attr("title","CP Enabled");
        //$("#priceList_elp > span.ui-iggrid-headertext").attr("title","Landing Price");
        // $("#priceList_DI > span.ui-iggrid-headertext").attr("title","Demand Index");
        // $("#priceList_MI > span.ui-iggrid-headertext").attr("title","Margin Index");
        // $("#priceList_CI > span.ui-iggrid-headertext").attr("title","Contribution Index");
    });



    $("#dc_id").change(function() {
    var csrf_token = $('#csrf-token').val();
    var dc_id = $('#dc_id').val();
    if(dc_id>0){
        $("#priceList").igGrid({dataSource: 'pricing/pricingdata?%24filter=dc_id+eq'+dc_id});
    }else{
        $("#priceList").igGrid({dataSource: 'pricing/pricingdata'});
    }
    
    });

    window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});


</script>


@stop
@extends('layouts.footer')