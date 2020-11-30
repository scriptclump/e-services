@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<span id="success_message_ajax"></span>
<div class="alert alert-success hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>           

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">            
        	<div class="portlet-title">
                <div class="caption">{{trans('product_color_config.heads.caption')}}</div>                
                <div class="actions">
                    	@if(isset($editdeletepermission) && $editdeletepermission ==1)
                        <a class="btn green-meadow" id="configureNewProdColor" href="#" data-toggle="modal">
                            <i class="fa fa-plus-circle"></i>
                            <span style="font-size:11px;"> {{trans('product_color_config.heads.add_color')}}</span>
                        </a>

                        <a class="btn green-meadow" id="" href="#import_prod_color" data-toggle="modal">                            
                            <span style="font-size:11px;"> {{trans('product_color_config.heads.import_color_config')}}</span>
                        </a>
                    	@endif
                </div>
            </div>
            <div class="portlet-body">
            	<div role="alert" id="alertStatus"></div>
            	@if(isset($editdeletepermission) && $editdeletepermission ==1)
				<table id="productColorConfigGrid"></table>
				@else
				<table id="productColorConfigGridExtended"></table>
				@endif
            </div>


            <div class="modal fade" id="editProductColorConfig" tabindex="-1" role="dialog" aria-labelledby="editProductColorConfigLabel" aria-hidden="true">
				    <div class="modal-dialog" role="document">
				        <div class="modal-content">
				            <div class="modal-header">
				                <h4 class="modal-title" id="editProductColorConfigLabel">{{trans('product_color_config.heads.edit_product_color')}}</h4>
				                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
				                <span aria-hidden="true">&times;</span>
				                </button>
				            </div>
				            <div class="modal-body">
				            	<div class="alert" role="alert" id="modalAlert"></div>
				                <form id="editProductColorConfigForm">
				                	<input type="hidden" name="_token" value="{{csrf_token()}}">
				                	<input type="hidden" name="edit_color_config_id" id="edit_color_config_id">
				                	
				                    <div class="row">
				                		<div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="Edit_WareHouse_Name" class="control-label">{{trans('product_color_config.side_heads.WareHouse_Name')}}
                                                <span class="required">*</span></label>
                                                
                                                <select class="form-control select2me" id="Edit_WareHouse_Name"
                                                name="Edit_WareHouse_Name" style="margin-top: 6px"
                                                placeholder="{{trans('product_color_config.side_heads.WareHouse_Name')}}">
                                                <option value ="">Please select ...</option>
                                                @foreach($wareHouseInfo as $wareHouse)
                                                    <option value = "{{$wareHouse->le_wh_id}}">{{$wareHouse->display_name}}</option>
                                                @endforeach
                                            	</select>
                                            </div>
                                        </div>
					                    <div class="col-lg-6">
					                		<div class="form-group">
						                        <label for="Edit_Product_Name" class="control-label">{{trans('product_color_config.side_heads.Product_Name')}}
						                        <span class="required">*</span></label>
						                        <input type="text" id="Edit_Product_Name" name="Edit_Product_Name" class="form-control" placeholder="SKU,Product Name,UPC" readonly/>
                            <input type="hidden" id="edit_product_id" class="form-control" placeholder="SKU,Product Name,UPC" />
						                    </div>
					                    </div>
				                    </div>
				                	<div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="Edit_Pack" class="control-label">{{trans('product_color_config.side_heads.Pack')}}
						                        <span class="required">*</span></label>
						                        
						                        <select  class="form-control"  name="Edit_Pack" id="Edit_Pack">
													<option value="">Please select ...</option>
												@foreach($packageLevel as $packageValue)
												<option value="{{$packageValue->value}}">{{$packageValue->name}}</option>
												@endforeach</select>
						                    </div>
				                		</div>
				                		<div class="col-md-6">
				                			<label for="Edit_Customer_Type" class="control-label">{{trans('product_color_config.side_heads.Customer_Type')}}
				                			<span class="required">*</span>
				                			</label>
				                			
                                            <select class="select2me form-control" id="Edit_Customer_Type" name="Edit_Customer_Type">
                                                <option value=''>Please select ...</option>
                                            @foreach($getCustomerGroup as $customerData)
                                            <option value = "{{$customerData->value}}">{{$customerData->master_lookup_name}}</option>
                                            @endforeach
                                            </select>
                                        </div>
				                    </div>
				                    <div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="Edit_Color" class="control-label">{{trans('product_color_config.side_heads.Color')}}
												<span class="required">*</span>
						                        </label>
						                        <select class="form-control" id="Edit_Color" name="Edit_Color">
                                                <option value=''>Please select ...</option>
	                                            @foreach($colors as $color)
	                                            <option value = "{{$color['value']}}">{{$color['master_lookup_name']}}</option>
	                                            @endforeach
	                                            </select>
						                    </div>
				                		</div>
				                	</div>
				                	<div class="row">
				                		<div class="col-lg-3">
				                			<div class="form-group">
						                        <label for="Edit_Elp" class="control-label">{{trans('product_color_config.side_heads.Elp')}}</label>
						                        <input type="text" class="form-control" id="Edit_Elp" name="Edit_Elp" placeholder="{{trans('product_color_config.side_heads.Elp')}}" readonly>
						                    </div>
				                		</div>
				                		<div class="col-lg-3">
				                			<div class="form-group">
						                        <label for="Edit_Esp" class="control-label">{{trans('product_color_config.side_heads.Esp')}}</label>
						                        <input type="text" class="form-control" id="Edit_Esp" name="Edit_Esp" placeholder="{{trans('product_color_config.side_heads.Esp')}}" readonly>
						                    </div>
				                		</div>
				                		<div class="col-lg-3">
				                			<div class="form-group">
						                        <label for="Edit_Margin" class="control-label">{{trans('product_color_config.side_heads.Margin')}}</label>
						                        <input type="text" class="form-control" id="Edit_Margin" name="Edit_Margin" placeholder="{{trans('product_color_config.side_heads.Margin')}}" readonly>
						                    </div>
				                		</div>
				                	</div>
				            </div>
				            <div class="modal-footer">
				                <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('product_color_config.heads.close')}}</button>
				                <button type="submit" id="saveProductColor" class="btn btn-primary">{{trans('product_color_config.heads.save')}}</button>
				            </div>
			                </form>
				        </div>
				    </div>
				</div>
            </div>
            <!-- Add Modal -->
            <div class="modal fade" id="addProductColorConfig" tabindex="-1" role="dialog" aria-labelledby="addProductColorConfigLabel" aria-hidden="true">
				    <div class="modal-dialog" role="document">
				        <div class="modal-content">
				            <div class="modal-header">
				                <h4 class="modal-title" id="addProductColorConfigLabel">{{trans('product_color_config.heads.add_product_color')}}</h4>
				                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
				                <span aria-hidden="true">&times;</span>
				                </button>
				            </div>
				            <div role="alert" id="alertStatusExists"></div>
				            <div class="modal-body">
				            	<div class="alert" role="alert" id="modalAlert"></div>
				                <form id="addProductColorConfigForm">
				                	<input type="hidden" name="_token" value="{{csrf_token()}}">
				                    <div class="row">
				                		<div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="Add_WareHouse_Name" class="control-label">{{trans('product_color_config.side_heads.WareHouse_Name')}}
                                                <span class="required">*</span></label>
                                                
                                                <select class="form-control select2me" id="Add_WareHouse_Name"
                                                name="Add_WareHouse_Name" style="margin-top: 6px"
                                                placeholder="{{trans('product_color_config.side_heads.WareHouse_Name')}}">
                                                <option value ="">Please select ...</option>
                                                @foreach($wareHouseInfo as $wareHouse)
                                                    <option value = "{{$wareHouse->le_wh_id}}">{{$wareHouse->display_name}}</option>
                                                @endforeach
                                            	</select>
                                            </div>
                                        </div>
					                    <div class="col-lg-6">
					                		<div class="form-group">
						                        <label for="Add_Product_Name" class="control-label">{{trans('product_color_config.side_heads.Product_Name')}}
						                        <span class="required">*</span></label>
						                        <input type="text" id="Add_Product_Name" name="Add_Product_Name" class="form-control" placeholder="SKU,Product Name,UPC" />
                            <input type="hidden" id="add_product_id" class="form-control" placeholder="SKU,Product Name,UPC" />
						                    </div>
					                    </div>
				                    </div>
				                	<div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="Add_Pack" class="control-label">{{trans('product_color_config.side_heads.Pack')}}
						                        <span class="required">*</span></label>
						                        
						                        <select  class="form-control"  name="Add_Pack" id="Add_Pack">
													<option value="">Please select ...</option>
												@foreach($packageLevel as $packageValue)
												<option value="{{$packageValue->value}}">{{$packageValue->name}}</option>
												@endforeach</select>
						                    </div>
				                		</div>
				                		<div class="col-md-6">
				                			<div class="form-group">
					                			<label for="Add_Customer_Type" class="control-label">{{trans('product_color_config.side_heads.Customer_Type')}}
					                			<span class="required">*</span>
					                			</label>
					                			
	                                            <select class="select2me form-control" id="Add_Customer_Type" name="Add_Customer_Type">
	                                                <option value=''>Please select ...</option>
	                                            @foreach($getCustomerGroup as $customerData)
	                                            <option value = "{{$customerData->value}}">{{$customerData->master_lookup_name}}</option>
	                                            @endforeach
	                                            </select>
                                        	</div>
                                        </div>

				                    </div>
				                    <div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="Add_Color" class="control-label">{{trans('product_color_config.side_heads.Color')}}
												<span class="required">*</span>
						                        </label>
						                        <select class="form-control" id="Add_Color" name="Add_Color">
                                                <option value=''>Please select ...</option>
	                                            @foreach($colors as $color)
	                                            <option value = "{{$color['value']}}">{{$color['master_lookup_name']}}</option>
	                                            @endforeach
	                                            </select>
						                    </div>
				                		</div>
				                	</div>
				                	<!-- <div class="row">
				                		<div class="col-lg-3">
				                			<div class="form-group">
						                        <label for="Add_Elp" class="control-label">{{trans('product_color_config.side_heads.Elp')}}</label>
						                        <input type="text" class="form-control" id="Add_Elp" name="Add_Elp" placeholder="{{trans('product_color_config.side_heads.Elp')}}">
						                    </div>
				                		</div>
				                		<div class="col-lg-3">
				                			<div class="form-group">
						                        <label for="Add_Esp" class="control-label">{{trans('product_color_config.side_heads.Esp')}}</label>
						                        <input type="text" class="form-control" id="Add_Esp" name="Add_Esp" placeholder="{{trans('product_color_config.side_heads.Esp')}}">
						                    </div>
				                		</div>
				                		<div class="col-lg-3">
				                			<div class="form-group">
						                        <label for="Add_Margin" class="control-label">{{trans('product_color_config.side_heads.Margin')}}</label
						                        >
						                        <input type="text" class="form-control" id="Add_Margin" name="Add_Margin" placeholder="{{trans('product_color_config.side_heads.Margin')}}">
						                    </div>
				                		</div>
				                	</div> -->
				            </div>
				            <div class="modal-footer">
				                <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('product_color_config.heads.close')}}</button>
				                <button type="submit" id="addProductColor" class="btn btn-primary">{{trans('product_color_config.heads.add')}}</button>
				            </div>
			                </form>
				        </div>
				    </div>
				</div>
            </div>			
        </div>
    </div>
</div>
<div class="modal modal-scroll fade in" id="import_prod_color" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="modalpopupclose" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Product Colors Upload</h4>
            </div>
            <div class="modal-body">
                	<form id="download_temp_form" action="/products/downloadProdColorConfigExcel">
                        <!-- <div class="row">
                            <div class="col-md-12 " align="pull-left">
                                <input type="checkbox" name="with_data"/> with data
                            </div>
                        </div>
                        <br> -->
                        <div class="row">
                            <div class="col-md-12 " align="center"> 
                                <button type="submit" id="download_template_button" role="button" class="btn green-meadow">Download Product Color Configuration Template</button>
                                <br/>
                                <p class="topmarg">Check With data to update product color information</p>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="row">
                        <div class="col-md-12 text-center" align="center">
                            <form id='import_cpenable_template_form' action="{{ URL::to('/products/uploadProdColorConfigExcel') }}" class="text-center" method="post" enctype="multipart/form-data">
                                <div class="fileUpload btn green-meadow"> <span id="up_text">Upload Product Color Configuration Template</span>
                                    <input type="file" class="form-control upload" name="import_file" id="upload_cpenable_file"/>
                                </div>
                                <span class="loader" id="dcloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>
                            <br/>
                                <p class="topmarg">Upload the filled product color configuration template</p>

                            </form>
                        </div>
                    </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<a class="btn green-meadow" data-toggle="modal" style="display:none" href="#import_messages">Show errors</a>
<div id="import_messages" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <table class='product_success_msg'>
                </table>
            </div>
        </div>
    </div>
</div>
{{HTML::style('css/switch-custom.css')}}

<style type="text/css">
.right-align-labels {    position: absolute;    right: 17px;    bottom: 50px;    color: blue;}
.rightAlign { text-align:right;}
    .ui-icon-check{color:#32c5d2 !important;}
    .ui-igcheckbox-small-off{color:#e7505a !important;}
    .fa-thumbs-o-up{color:#3598dc !important;}
	.fa-rupee{color:#3598dc !important;}
    .fa-pencil{color:#3598dc !important;}
    .fa-trash-o{color:#3598dc !important;}
	.ui-iggrid-featurechooserbutton{display:none !important}
	.ui-icon.ui-corner-all.ui-icon-pin-w{display:none !important}
	.fa-fast-forward{color:#3598dc !important;}

#extendedProductsGrid_Action {text-align:center !important;}
#productsGrid_Action {text-align:center !important;}
.sorting1 a{ list-style-type:none !important;   font-size:12px;}
.sorting1 a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
.sorting1 a:active{text-decoration:underline !important;border-bottom:1px black;}
.active{text-decoration: underline !important; border-bottom:1px black}
.inactive{text-decoration:none !important; color:#8f8c8c !important;}
.caption.status{ margin-left: 84px;}
.ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{border-spacing:0px !important;}
.centerAlign { text-align:center;}
.ui-autocomplete{
	z-index: 10100 !important; top:10px;  height:200px; overflow-y: scroll; overflow-x:hidden; border-top:none!important;position:fixed !important;
}


.ui-autocomplete li{ border-bottom:1px solid #efefef; padding-top:10px!important; padding-bottom:10px!important; 
}
label {
    padding-bottom: 5px;
}
.has-feedback .form-control {
   padding-right: 10px;
}
</style>


 
@stop
@section('style')
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript')
@include('includes.ignite')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<!-- <script src="{{ URL::asset('assets/global/plugins/getcategorylist.js') }}" type="text/javascript"></script>
 -->
<!-- <script src="{{ URL::asset('assets/admin/pages/scripts/product/products_grid_script.js') }}" type="text/javascript"></script> commented by rajesh-->

<!-- <script src="{{ URL::asset('assets/global/plugins/get-brands-dropdown.js') }}" type="text/javascript"></script> -->
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
<!-- <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> -->
<!-- <script src="{{ URL::asset('assets/admin/pages/scripts/price/priceModel.js') }}" type="text/javascript"></script> commented by rajesh-->
<script>
$(document).ready(function(){

	$('#upload_cpenable_file').change(function()
	{
    	$('#import_cpenable_template_form').css('border', '');
    	$('#import_cpenable_template_form').submit();
    });
    $("#import_prod_color").on('hide.bs.modal', function () {
    	$('#import_cpenable_template_form')[0].reset();
    });

    $('#import_cpenable_template_form').submit(function(e){
	    e.preventDefault();
	    var csrf_token = $('#csrf-token').val();
	    var formData = new FormData($(this)[0]);
	    $('#pimloader').show();
	    var url = $(this).attr('action');
	    $.ajax({
	    		headers: {'X-CSRF-TOKEN': csrf_token},
	            url: url,
	            type: 'POST',
	            data: formData,
	            async: false,
	            beforeSend: function(xhr) {
	            	$('#pimloader').show();
	            },
	            success: function (data) {
		            $('.close').trigger('click');
		            $('#pimloader').hide();
		            var data = jQuery.parseJSON(data);

		            console.log(data);
		            if (data.status_messages.length > 0)
					{
						$('.product_success_msg').html('');
		            	$('a[href="#import_messages"]').trigger('click');
						$.each(data.status_messages, function(key, val){
						    $('.product_success_msg').append('<tr><td>' + val + '</td></tr>');
						});
					}  
	            },
	            cache: false,
	            contentType: false,
	            processData: false
	    });
    });

	$.ajaxSetup({
		        headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
		      });	
	$('#addProductColorConfig').on('hide.bs.modal', function () {
            $("#addProductColorConfigForm").bootstrapValidator('resetForm', true);
            $(".modal-backdrop").remove();

            // reset the form field values
            $("#Add_WareHouse_Name").select2("val", "");
            $("#Add_Customer_Type").select2("val", "");
            $("#Add_Product_Name").val('');
            $("#add_product_id").val('');

            $("#Add_Pack").val('');
            $("#Add_Color").val('');
         /*   $("#Add_Elp").val('');
            $('#Add_Esp').val('');
            $("#Add_Margin").val('');*/

    });

    $("#configureNewProdColor").click(function(){
        $("#addProductColorConfig").modal("show");
        $("#addProductColorConfig").modal({backdrop:'static', keyboard:false});
    });

    // Hiding the Alert on Page Load
    $("#modalAlert").hide();
    $("#alertStatus").hide();
    $("#alertStatusExists").hide();
    $('.modal-backdrop').remove();

    $("#modalClose").click(function(){
        $("#modalAlert").hide();
        $('#modalAlert').data('bs.modal',null); // this clears the BS modal data
    });

	$('#productColorConfigGrid').igGrid({
	    dataSource: 'getProductColorConfig',
	    autoGenerateColumns: false,
	    autoGenerateLayouts: false,
	    mergeUnboundColumns: false,
	    responseDataKey: 'Records',
	    generateCompactJSONResponse: false,
	    rowHeight:12,
	    enableUTCDates: true,
	    expandColWidth: 0,
	    renderCheckboxes: true,
	    columns: [
	        {headerText: '{{trans('product_color_config.side_heads.WareHouse_Name')}}', key: 'DisplayName', dataType: 'string', width: '175px'},
			{headerText: '{{trans('product_color_config.side_heads.Product_Name')}}', key: 'ProductName', dataType: 'string', width: '275px'},
	        {headerText: '{{trans('product_color_config.side_heads.Pack')}}', key: 'Pack', dataType: 'string', width:'100px'},
			{headerText: '{{trans('product_color_config.side_heads.Customer_Type')}}', key: 'CustomerType', dataType: 'string', width: '150px'},
	        {headerText: '{{trans('product_color_config.side_heads.Color')}}', key: 'Color', dataType: 'string', width: '100px'},
	        {headerText: '{{trans('product_color_config.side_heads.Elp')}}', key: 'Elp', dataType: 'number', width: '100px'},
	        {headerText: '{{trans('product_color_config.side_heads.Esp')}}', key: 'Esp', dataType: 'number', width: '100px'},
	        {headerText: '{{trans('product_color_config.side_heads.Margin')}}', key: 'Margin', dataType: 'number', width: '100px'},
	        {headerText: 'Actions', key: 'Action', dataType: 'string', width: '110px'},
	    ],
	    features: [
	                    {
	                        name: "ColumnFixing",
	                        fixingDirection: "right",
	                        columnSettings: [
	                            {
	                                columnKey: "Action",
	                                isFixed: true,
	                                allowFixing: false
	                            }
	                        ]
	                    },
	        
	        {
	            name: "Filtering",
	            type: "remote",
	            mode: "simple",
	            filterDialogContainment: "window",
	            columnSettings: [
	                /*{columnKey: 'ProductLogo', allowFiltering: false},
	                {columnKey: 'Schemes', allowFiltering: false},
					{columnKey: 'Statuss', allowFiltering: false},*/
	                {columnKey: 'Action', allowFiltering: false},
	            ]
	        },
	        {
	            name: 'Sorting',
	            type: 'remote',
	            persist: false,
	            columnSettings: [
	                /*{columnKey: 'ProductLogo', allowSorting: false},
	                {columnKey: 'Schemes', allowSorting: false},
					{columnKey: 'Statuss', allowSorting: false},*/
	                {columnKey: 'Action', allowSorting: false},
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
	    primaryKey: 'ColorWhId',
	    width: '100%', 
	    height: '520px',
	    initialDataBindDepth: 0,
	    localSchemaTransform: false
	}); 
	$('#productColorConfigGridExtended').igGrid({
	    dataSource: 'getProductColorConfig',
	    autoGenerateColumns: false,
	    autoGenerateLayouts: false,
	    mergeUnboundColumns: false,
	    responseDataKey: 'Records',
	    generateCompactJSONResponse: false,
	    rowHeight:12,
	    enableUTCDates: true,
	    expandColWidth: 0,
	    renderCheckboxes: true,
	    columns: [
	        {headerText: 'WAREHOUSE NAME', key: 'DisplayName', dataType: 'string', width: '175px'},
			{headerText: 'PRODUCT NAME', key: 'ProductName', dataType: 'string', width: '275px'},
	        {headerText: 'PACK', key: 'Pack', dataType: 'string', width:'100px'},
			{headerText: 'CUSTOMER TYPE', key: 'CustomerType', dataType: 'string', width: '150px'},
	        {headerText: 'COLOR', key: 'Color', dataType: 'string', width: '100px'}
	    ],
	    features: [
	                    {
	                        name: "ColumnFixing",
	                        fixingDirection: "right",
	                        columnSettings: [
	                            {
	                                // columnKey: "Action",
	                                // isFixed: true,
	                                // allowFixing: false
	                            }
	                        ]
	                    },
	        
	        {
	            name: "Filtering",
	            type: "remote",
	            mode: "simple",
	            filterDialogContainment: "window",
	            columnSettings: [
	                /*{columnKey: 'ProductLogo', allowFiltering: false},
	                {columnKey: 'Schemes', allowFiltering: false},
					{columnKey: 'Statuss', allowFiltering: false},*/
	                {columnKey: 'Action', allowFiltering: false},
	            ]
	        },
	        {
	            name: 'Sorting',
	            type: 'remote',
	            persist: false,
	            columnSettings: [
	                /*{columnKey: 'ProductLogo', allowSorting: false},
	                {columnKey: 'Schemes', allowSorting: false},
					{columnKey: 'Statuss', allowSorting: false},*/
	                {columnKey: 'Action', allowSorting: false},
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
	    primaryKey: 'ColorWhId',
	    width: '100%', 
	    height: '520px',
	    initialDataBindDepth: 0,
	    localSchemaTransform: false
	}); 

/*    autosuggest();
    function autosuggest(){
        $( "#Edit_Product_Name" ).autocomplete({
            source: '/products/getProductNames',
            minLength: 2,
            params: { 
             	// entity_type:$('#supplier_list').val() 
         	},
            select: function( event, ui ) {
                  if(ui.item.label=='No Result Found'){
                     event.preventDefault();
                  }
                  alert(ui.item.product_id);
                  $('#edit_product_id').val(ui.item.product_id);
            }
        });
    }*/

	autosuggest_for_add();
    function autosuggest_for_add(){
        $( "#Add_Product_Name" ).autocomplete({
            source: '/products/getProductNames',
            minLength: 2,
            params: { 
             	// entity_type:$('#supplier_list').val() 
         	},
            select: function( event, ui ) {
                  if(ui.item.label=='No Result Found'){
                     event.preventDefault();
                  }
                  
                  $('#add_product_id').val(ui.item.product_id);
            }
        });
    }

    //update modal validations
    $('#editProductColorConfigForm')
        .bootstrapValidator({
        	framework: 'bootstrap',
        	fields: {
        		Edit_WareHouse_Name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('product_color_config.validation_errors.WareHouse_Name')}}"
                        }
                    }
                },
                Edit_Product_Name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('product_color_config.validation_errors.Product_Name')}}"
                        }
                    }
                },
                Edit_Pack: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('product_color_config.validation_errors.Pack')}}"
                        }
                    }
                },
                Edit_Customer_Type: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('product_color_config.validation_errors.Customer_Type')}}"
                        }
                    }
                },
                Edit_Color: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('product_color_config.validation_errors.Color')}}"
                        }
                    }
                },
                Edit_Elp: {
                    validators: {
	                    notEmpty: {
	                        message: 'Required',
	                    },
	                    numeric: {
	                        message: "{{trans('product_color_config.validation_errors.Elp_isdigit')}}",
	                    },
	                    greaterThan: {
	                        value: -1,
	                        message: 'ELP must be greater than or equals to 0'
	                    },
	                    callback: {
	                        message: 'ELP is not a number!',
	                        callback: function(value, validator, $field) {
	                            var elp = parseFloat(value).toFixed(2);
	                            
	                            if(!isNaN(elp))
	                            {
	                                return true;
	                            }else{
	                                return false; 
	                            }

	                        }
	                    }
                    }
                },
                Edit_Esp: {
                    validators: {
	                    notEmpty: {
	                        message: 'Required',
	                    },
	                    numeric: {
	                        message: "{{trans('product_color_config.validation_errors.Esp_isdigit')}}",
	                    },
	                    greaterThan: {
	                        value: -1,
	                        message: 'ESP must be greater than or equals to 0'
	                    },
	                    callback: {
	                        message: 'ESP is not a number!',
	                        callback: function(value, validator, $field) {
	                            var elp = parseFloat(value).toFixed(2);
	                            
	                            if(!isNaN(elp))
	                            {
	                                return true;
	                            }else{
	                                return false; 
	                            }

	                        }
	                    }
                    }
                },
                Edit_Margin: {
                    validators: {
	                    notEmpty: {
	                        message: 'Required',
	                    },
	                    numeric: {
	                        message: "{{trans('product_color_config.validation_errors.Margin_isdigit')}}",
	                    },
	                    greaterThan: {
	                        value: -1,
	                        message: 'Margin must be greater than or equals to 0'
	                    },
	                    callback: {
	                        message: 'Margin is not a number!',
	                        callback: function(value, validator, $field) {
	                            var elp = parseFloat(value).toFixed(2);
	                            
	                            if(!isNaN(elp))
	                            {
	                                return true;
	                            }else{
	                                return false; 
	                            }

	                        }
	                    }
                    }
                }
        	}
        })
        .on('success.form.bv', function(event) {
        	event.preventDefault();

			$('#Edit_WareHouse_Name').prop('disabled', false);
			$('#Edit_Pack').prop('disabled', false);
			$('#Edit_Customer_Type').prop('disabled', false);

        	var newProdColorConfigData = {
                Edit_WareHouse_Name : $("#Edit_WareHouse_Name").val(),
                edit_product_id: $("#edit_product_id").val(),
                Edit_Pack: $("#Edit_Pack").val(),
                Edit_Customer_Type: $("#Edit_Customer_Type").val(),
                Edit_Color: $("#Edit_Color").val(),
                Edit_Elp: $("#Edit_Elp").val(),
                Edit_Esp: $('#Edit_Esp').val(),
                Edit_Margin : $("#Edit_Margin").val(),
                Primary_Id : $("#edit_color_config_id").val()
            };
            
            $.post('/products/updateProdColorConfig',newProdColorConfigData,function(response){
                $("#editProductColorConfig").modal("hide");

                if(response.status){
                    $("#alertStatus").attr("class","alert alert-success").text("{{trans('product_color_config.message.success_updated')}}").show().delay(3000).fadeOut(350);
                    $('#productColorConfigGrid').igGrid("dataBind");
                }
                else
                {
                    $("#alertStatus").attr("class","alert alert-danger").text("{{trans('product_color_config.message.failed_updated')}}").show().delay(3000).fadeOut(350);
                }
            },'json'); 
        });

    //add modal validations
    $('#addProductColorConfig')
        .bootstrapValidator({
        	framework: 'bootstrap',
        	fields: {
        		Add_WareHouse_Name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('product_color_config.validation_errors.WareHouse_Name')}}"
                        }
                    }
                },
                Add_Product_Name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('product_color_config.validation_errors.Product_Name')}}"
                        }
                    }
                },
                Add_Pack: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('product_color_config.validation_errors.Pack')}}"
                        }
                    }
                },
                Add_Customer_Type: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('product_color_config.validation_errors.Customer_Type')}}"
                        }
                    }
                },
                Add_Color: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('product_color_config.validation_errors.Color')}}"
                        }
                    }
                }
                /*,
                Add_Elp: {
                    
                    validators: {
	                    notEmpty: {
	                        message: 'Required',
	                    },
	                    numeric: {
	                        message: "{{trans('product_color_config.validation_errors.Elp_isdigit')}}",
	                    },
	                    greaterThan: {
	                        value: -1,
	                        message: 'ELP must be greater than or equals to 0'
	                    },
	                    callback: {
	                        message: 'ELP is not a number!',
	                        callback: function(value, validator, $field) {
	                            var elp = parseFloat(value).toFixed(2);
	                            
	                            if(!isNaN(elp))
	                            {
	                                return true;
	                            }else{
	                                return false; 
	                            }

	                        }
	                    }
                    }
                },
                Add_Esp: {
                    validators: {
	                    notEmpty: {
	                        message: 'Required',
	                    },
	                    numeric: {
	                        message: "{{trans('product_color_config.validation_errors.Esp_isdigit')}}",
	                    },
	                    greaterThan: {
	                        value: -1,
	                        message: 'ESP must be greater than or equals to 0'
	                    },
	                    callback: {
	                        message: 'ESP is not a number!',
	                        callback: function(value, validator, $field) {
	                            var elp = parseFloat(value).toFixed(2);
	                            
	                            if(!isNaN(elp))
	                            {
	                                return true;
	                            }else{
	                                return false; 
	                            }

	                        }
	                    }
                    }
                },
                Add_Margin: {
                    validators: {
	                    notEmpty: {
	                        message: 'Required',
	                    },
	                    numeric: {
	                        message: "{{trans('product_color_config.validation_errors.Margin_isdigit')}}",
	                    },
	                    greaterThan: {
	                        value: -1,
	                        message: 'Margin must be greater than or equals to 0'
	                    },
	                    callback: {
	                        message: 'Margin is not a number!',
	                        callback: function(value, validator, $field) {
	                            var elp = parseFloat(value).toFixed(2);
	                            
	                            if(!isNaN(elp))
	                            {
	                                return true;
	                            }else{
	                                return false; 
	                            }

	                        }
	                    }
                    }
                }*/
        	}
        })
        .on('success.form.bv', function(event) {
        	event.preventDefault();

        	var newProdColorConfigData = {
                Add_WareHouse_Name : $("#Add_WareHouse_Name").val(),
                add_product_id: $("#add_product_id").val(),
                Add_Pack: $("#Add_Pack").val(),
                Add_Customer_Type: $("#Add_Customer_Type").val(),
                Add_Color: $("#Add_Color").val()
                /*,
                Add_Elp: $("#Add_Elp").val(),
                Add_Esp: $('#Add_Esp').val(),
                Add_Margin : $("#Add_Margin").val()*/
            };

            //check if the entered product is already rated or not. 
            if(newProdColorConfigData.Add_WareHouse_Name!="" && 
             newProdColorConfigData.add_product_id!="" &&
             newProdColorConfigData.Add_Pack!="" &&
             newProdColorConfigData.Add_Customer_Type!="")
            $.ajax({
	            async: false,
	            type: "POST",
	            url: "/products/checkProdColorConfig",
	            data: newProdColorConfigData,
	            dataType: "json",
	            success: function (response) {
	                if(response.valid == true)
	            	{
	            		/*$.post('/products/addProdColorConfig',newProdColorConfigData,function(response){
		                $("#addProductColorConfig").modal("hide");
		                $(".modal-backdrop").remove();

		                if(response.status){
		                    $("#alertStatus").attr("class","alert alert-success").text("{{trans('product_color_config.message.success_new')}}").show().delay(3000).fadeOut(350);
		                    $('#productColorConfigGrid').igGrid("dataBind");
		                }
		                else
		                    $("#alertStatus").attr("class","alert alert-danger").text("{{trans('product_color_config.message.failed_new')}}").show().delay(3000).fadeOut(350);
		            	},'json');	*/
		            	$.ajax({
				            async: false,
				            type: "POST",
				            url: "/products/addProdColorConfig",
				            data: newProdColorConfigData,
				            dataType: "json",
				            success: function (response) {
				            	$("#addProductColorConfig").modal("hide");
		                		$(".modal-backdrop").remove();
				                if(response.status)
				            	{
				            		$("#alertStatus").attr("class","alert alert-success").text("{{trans('product_color_config.message.success_new')}}").show().delay(3000).fadeOut(350);
		                    		$('#productColorConfigGrid').igGrid("dataBind");
				            	}
				            	else
				            	{
				            		$("#alertStatus").attr("class","alert alert-danger").text("{{trans('product_color_config.message.failed_new')}}").show().delay(3000).fadeOut(350);
				            	}
				        	}
				        });

	            	}
	            	else
	            	{
	            		$("#alertStatusExists").attr("class","alert alert-danger").text("{{trans('product_color_config.validation_errors.Color_exist')}}").show().delay(3000).fadeOut(350);
	            		$("#addProductColor").attr("disabled",false);
	            	}
	            }
        	});		

            /*$.post('/products/checkProdColorConfig',newProdColorConfigData,function(response){
            	if(response.valid == true)
            	{
            		$.post('/products/addProdColorConfig',newProdColorConfigData,function(response){
	                $("#addProductColorConfig").modal("hide");
	                $(".modal-backdrop").remove();

	                if(response.status){
	                    $("#alertStatus").attr("class","alert alert-success").text("{{trans('product_color_config.message.success_new')}}").show().delay(3000).fadeOut(350);
	                    $('#productColorConfigGrid').igGrid("dataBind");
	                }
	                else
	                    $("#alertStatus").attr("class","alert alert-danger").text("{{trans('product_color_config.message.failed_new')}}").show().delay(3000).fadeOut(350);
	            	},'json');	
            	}
            	else
            	{
            		$("#alertStatusExists").attr("class","alert alert-danger").text("{{trans('product_color_config.validation_errors.Color_exist')}}").show().delay(3000).fadeOut(350);
            		$("#addProductColor").attr("disabled",false);
            	}
            	
            },'json');*/
             
        });
});

function editProdColorConfigRecord(id) {
 		$("#editProductColorConfig").modal("show");
 		$('#editProductColorConfig').modal({backdrop:'static', keyboard:false});

 		$.post('/products/editProdColorConfig/'+id,function(response){
 			if(response.status){
 				$("#edit_color_config_id").val(id);
 				$("#Edit_WareHouse_Name").select2('val',response.WareHouse_Name);
 				$("#Edit_Product_Name").val(response.Product_Name);
 				$("#edit_product_id").val(response.Product_Id);
 				$("#Edit_Pack").val(response.Pack);
 				$("#Edit_Customer_Type").select2('val',response.Customer_Type);
 				$("#Edit_Color").val(response.Color);

 				$('#Edit_WareHouse_Name').prop('disabled', true);
 				$('#Edit_Pack').prop('disabled', true);
 				$('#Edit_Customer_Type').prop('disabled', true);

 				$("#Edit_Elp").val(response.Elp);
 				$("#Edit_Esp").val(response.Esp);
 				$("#Edit_Margin").val(response.Margin);

 			}
 			else{
 				$("#modalAlert").addClass("alert-danger").text("{{trans('hsn.message.invalid')}}").show();
 			}
 		},'json');
 	}

	function deleteProdColorConfifRecord(id) {
		var decision = confirm("Are you sure. Do you want to Delete it!");
		if(decision){
		    $.post('/products/delProdColorConfig/'+id,function(response){
		        if(response.status){
		            $("#alertStatus").attr("class","alert alert-info").text("{{trans('product_color_config.message.success_deleted')}}").show().delay(3000).fadeOut(350);
		            $('#productColorConfigGrid').igGrid("dataBind");
		        }
		        else{
		            $("#alertStatus").attr("class","alert alert-danger").text("{{trans('product_color_config.message.failed_deleted')}}").show().delay(3000).fadeOut(350);
		        }
	    	},'json');
		}
	}
</script>
@stop
@section('style')
.ui-iggrid .ui-iggrid-filtercell .ui-igedit{width: auto !important; height:50px !important;}
@stop
@extends('layouts.footer')
