@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Promotion Dashboard'); ?>
<span id="success_message">@include('flash::message')</span>
<span id = "success_message1"></span>
<div class="row">
<div class="col-md-12 col-sm-12">
<div class="portlet light tasks-widget" style="height:630px;">
<div class="portlet-title">
	<div class="caption">
		Promotion Dashboard Details 
    </div>
        <div class="actions">
         @if($addPromotionAcess == '1')
        <a href="/promotions/addnewpromotion" class="btn green-meadow">Add New Promotion</a>
        @endif

        <a href="/promotions/slabreport" class="btn green-meadow" target="_blank">Download Slab</a>
        
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
        @if($uploadAccess == '1')
        <a href="#" data-id="#" data-toggle="modal" data-target="#upload-slab" class="btn green-meadow">Upload Slab Price</a>
        @endif


        <a href="/promotions/getallpromotiondata" id="download_all_promotions" class="btn green-meadow">Download All Promotions</a>



    </div>
</div>
<div class="portlet-body">

<div class="row">
	<div class="col-md-12">
	<div class="table-scrollable">
	<table id="promotionlist"></table>
	</div>
	</div>
</div>

<!-- Module for upload and download -->

<div class="modal fade" id="upload-slab" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">SLAB UPLOAD</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet box">
                                <div class="portlet-body">

                                    {{ Form::open(array('url' => '/promotions/downloadexcelforslabpromotion', 'id' => 'downloadexcel-slabpromotion'))}}

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
                                                <label class="control-label">State</label>

                                                <select id = "mdl_state"  name = "mdl_state" class="form-control" >
                                                        <!-- <option value = "all">All</option> -->
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
                                                        <!-- <option value = "all">All</option> -->
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
                                                <button type="submit" class="btn green-meadow" id="download-excel">Download Slab Upload Template</button>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    {{ Form::close() }}


                                    {{ Form::open(['id' => 'frm_promotion_slab']) }}
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                        <input type="file" name="upload_slabfile" id="upload_slabfile" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button"  class="btn green-meadow" id="slab-promotion-upload-button">Upload Slab Promotion</button>
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
</div>

@stop

@section('userscript')
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/viewpromotion.js') }}" type="text/javascript"></script>

@stop
@extends('layouts.footer')