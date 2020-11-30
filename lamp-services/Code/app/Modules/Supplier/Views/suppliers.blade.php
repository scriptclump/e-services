@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="alert alert-success hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div><div></div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                    <?php 
                    if (!empty(session('legalentity_id'))) {
                        echo "Edit ".$vendor;
                    } else {
                        echo"Create ".$vendor;
                    } ?>
                </div>
                <div class="tools"> <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span> </div>
            </div>           
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="tabbable-line">
                            <ul class="nav nav-tabs ">
                                <li class="active"><a href="#tab_11" data-toggle="tab">{{$vendor}} Information</a></li>
                                <li><a href="#tab_22" data-toggle="tab" class="supp_info">Documents</a></li>
                                <li><a href="#tab_22_1" data-toggle="tab" class="tab-pane">Agreement Terms</a></li>
                                @if($vendor == 'Supplier')                                
                                <li><a href="#tab_33" data-toggle="tab">Subscribe Products</a></li>
                                <li><a href="#tab_44" data-toggle="tab" class="tot_product">ToT</a></li>
                                <li><a href="#tab_66" data-toggle="tab">Approval History</a></li>
                                @elseif($vendor == 'Vehicle'|| $vendor == 'Space')
                                <li><a href="#tab_77" data-toggle="tab">Additional Information</a></li>
                                @endif
                                <!-- 
                                <li><a href="#tab_44_1" data-toggle="tab">DC Inventory</a></li>
                                <li><a href="#tab_55" data-toggle="tab">Warehouses</a></li>-->
                                @if(isset($leId))
                                @include('PurchaseOrder::Form.paymentTab')
                                @endif
                            </ul>
                            <div class="tab-content headings">
                                <input type="hidden" name="supplier_approval_hidden" id="supplier_approval_hidden" value="@if(isset($approval)){{$approval}}@endif">
                                @include('Supplier::supplierinfo')
                                @include('Supplier::documents')
                                @include('Supplier::agreement_terms')
                                @if($vendor == 'Supplier')                                
                                @include('Supplier::brands')
                                @include('Supplier::tot')
                                @include('Supplier::history')
                                @endif
                                 @include('Supplier::additional')
                                 @if(isset($leId))
                                 @include('PurchaseOrder::Form.legalentitypayments')
                                 @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="csrf-token" name="_Token" value="{{ csrf_token() }}">

<div class="modal fade modal-scroll in" id="addp" tabindex="-1" role="addlp" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="close"></button>
                <h4 class="modal-title add_totprd" id="add_totprd"></h4>
            </div>
            <form action="" method="post" id="supplier_add_products">
                <input type="hidden" class="form-control" name="edit_form_product_id" id="edit_form_product_id">
                <input type="hidden" class="form-control" name="product_tot_id" id="product_tot_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Warehouse <span class="required" aria-required="true">*</span></label>
                                <select class="form-control spdt_whid" name="spd_whid" id="spd_whid">
                                    <option value="">Select Warehouse</option>
                                    @if(isset($legalentity_warehouses))                    
                                    @foreach($legalentity_warehouses as $Val )
                                    <option value="{{$Val['le_wh_id']}}">{{$Val['lp_wh_name']}}</option>
                                    @endforeach
                                    @endif
                                </select>

                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Brand </label>
                                <!--<input type="text" class="form-control" name="brand" id="tot_brand_name">-->
                                <select class="form-control" name="brand" id="brand">
                                    <option value="">Select Brand</option>

                                    @foreach($brands as $brand )

                                    <option value="{{$brand['brand_id']}}">{{$brand['brand_name']}}</option>

                                    @endforeach 

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Category</label>
                                <select class="form-control" name="category" id="tot_cat_name">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Product Title </label>
                                <select class="form-control" name="product_name" id="tot_product_name">
                                    <option value="">Select Product</option>
                                </select>
                                <input type="hidden" id="prod_name" name="prod_name" value="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Product Name <span class="required" aria-required="true">*</span></label>
                                <input type="text" class="form-control" name="product_title" id="tot_product_title">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Supplier Product Code</label>
                                <input type="text" class="form-control" name="supplier_sku_code" id="supplier_sku_code">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ trans('headings.LP') }}<span class="required" aria-required="true">*</span></label>
                                <input type="text" class="form-control" name="dlp" id="dlp">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Margin(%)</label>
                                <input type="text" class="form-control" name="distributor_margin" id="distributor_margin">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">PTR</label>
                                <input type="text" class="form-control" name="rlp" id="rlp">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Supplier DC Relationship </label>
                                <select class="form-control" name="supplier_dc_relationship" id="supplier_dc_relationship">
                                    <option value="">Select DC Relationship</option>
                                    @foreach($suppliers_dcrealtionship as $suppliers_dcrealtionship)
                                    <option value="{{$suppliers_dcrealtionship->value}}">{{$suppliers_dcrealtionship->account_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">GRN Freshness Percentage</label>
                                <input type="text" class="form-control" name="grn_freshness_percentage" id="grn_freshness_percentage">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">TAX TYPES</label>
                                <select class="form-control" id="tax_type" name="tax_type">
                                    <option value="">Select Tax Type</option>    
                                    @foreach($tax_types as $tax_types )
                                    <option value="{{$tax_types->value}}" >{{$tax_types->account_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">TAX%</label>
                                <input type="text" class="form-control" name="tax" id="tax">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">MOQ</label>
                                <input type="text" class="form-control" name="moq" id="moq">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">MOQ UoM</label>
                                <select class="form-control" id="moq_uom" name="moq_uom">
                                    <option value="">Select MoQ UoM</option>    
                                    @foreach($moq_data as $moq_data )
                                    <option value="{{$moq_data->value}}" >{{$moq_data->account_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Delivery TAT</label>
                                <input type="text" class="form-control" name="delivery_terms" id="delivery_terms">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Delivery TAT UoM</label>
                                <select class="form-control" id="delivery_tat_uom" name="delivery_tat_uom">
                                    <option value="">Select Delivery TAT UoM</option>    
                                    @foreach($uom_data as $uomVal )
                                    <option value="{{$uomVal->value}}" >{{$uomVal->account_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">GRN DAYS</label>
                                <select class="form-control multi-select-search-box sumoTot common" multiple="multiple" 
                                        placeholder='Select GRN Days' id="grn_days" name="grn_days[]">
                                    <option value="SUNDAY">SUNDAY</option>    
                                    <option value="MONDAY">MONDAY</option>
                                    <option value="TUESDAY">TUESDAY</option>
                                    <option value="WEDNESDAY">WEDNESDAY</option>
                                    <option value="THURSDAY">THURSDAY</option>
                                    <option value="FRIDAY">FRIDAY</option>
                                    <option value="SATURDAY">SATURDAY</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">RTV Allowed</label>
                                <div id="return_accepted">
                                    <input type="radio" value="1" name="rtv_allowed" id="rtv_allowed">
                                    Yes
                                    <input type="radio" value="0" name="rtv_allowed" id="rtv_allowed" checked>
                                    No </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Inventory Mode </label>
                                <select class="form-control" name="inventory_mode" id="inventory_mode">
                                    <option value="">Select Inventory Mode</option>

                                    @if(isset($inventory_data))
                                    @foreach($inventory_data as $inv)

                                    <option value="{{$inv->value}}">{{$inv->account_type}}</option>

                                    @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">ATP</label>
                                <input type="text" class="form-control" name="atp" id="atp">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">ATP Period</label>
                                <select class="form-control" name="atp_period" id="atp_period">
                                    <option value="">Select Inventory Mode</option>

                                    @if(isset($atp_peyiod))
                                    @foreach($atp_peyiod as $atp_peyiod)
                                    <option value="{{$atp_peyiod->value}}">{{$atp_peyiod->account_type}}</option>
                                    @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">KVI</label>
                                <select class="form-control kvit" name="kvi" id="kvi">
                                    <option value="">Select KVI</option>
                                    @foreach($kvi as $kvi)
                                    <option value="{{$kvi->value}}">{{$kvi->account_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Is Prefered Supplier</label>
                                <input type="radio" name="is_preferred_supplier" id="is_preferred_supplier" value="1" checked> Yes
                                <input type="radio" name="is_preferred_supplier" id="is_preferred_supplier" value="0"> No
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Effective Date<span class="required" aria-required="true">*</span></label>
                                <input type="text" class="form-control" name="efet_dat" id="efet_dat">
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn green-meadow">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.modal-content --> 
    </div>
    <!-- /.modal-dialog --> 
</div>

@include('Product::totprice')
@if(isset($leId)) 
@include('PurchaseOrder::Form.paymentApprovalPopup')
@include('PurchaseOrder::Form.paymentHistoryPopup')
@endif
<input type="text" id="supplier_id" value="<?php echo Session::get('supplier_id'); ?>" hidden />
<input type="text" id="legalentity_id" value="<?php echo Session::get('legalentity_id'); ?>" hidden />
@stop
@section('style')
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css')}} rel="stylesheet" type="text/css" />
      <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css')}} rel="stylesheet" type="text/css" />
      <link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .parent_cat{font-size:14px !important;}
    .sub_cat{ font-size:13px !important; padding-left:3px !important; background:#fff !important;}
    .prod_class{padding-left:10px !important; background:#fff !important; }

    .select2-disabled{font-weight:bold !important;}


    .thumbnail {
        padding: 0px !important;
        margin-bottom: 0px !important;
    }
    .fileinput-filename{word-wrap: break-word !important;}

    .fileinput-new .thumbnail{ width:100px !important; height:33px !important;}

    h4.block{padding:0px !important; margin:0px !important; padding-bottom:10px !important;}

    .pac-container .pac-logo{    z-index: 9999999 !important;}
    .pac-container{    z-index: 9999999 !important;}
    .pac-logo{    z-index: 9999999 !important;}
    #dvMap{height:304px !important; width:269px !important;}

    label {
        margin-bottom: 0px !important;
        padding-bottom: 0px !important;
    }




    #supplier_tot_grid_container {
        width: 100% !important;
    }

    .form-group {
        margin-bottom: 5px !important;
    }
    .SumoSelect > .CaptionCont > span.placeholder {
        font-weight:normal !important;
    }
    .radioborder{border:none !important;}
    .radio input[type=radio]{ margin-left:0px !important;}
    .thumbnail{border:none !important;}
    label{margin-bottom:10px !important;}
    .fa-check{color:#32c5d2 !important;}
    .fa-times{color:#e7505a !important;}
    .fa-thumbs-o-up{color:#3598dc !important;}
    .fa-pencil{color:#3598dc !important;}
    .fa-trash-o{color:#3598dc !important;}
	#documentType-error, #manuLogo-error, #ref_no-error{color:#e02222 !important}
</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/jquery-tags-input/jquery.tagsinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/typeahead/typeahead.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/components-rounded.css') }}" id="style_components" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/plugins.css') }}" rel="stylesheet" type="text/css" />

@stop
@section('script') 
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/get-brands-dropdown.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/typeahead/typeahead.bundle.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/getcategorylist.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-supplier.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/payments_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/approvalscript.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    jQuery(document).ready(function () {
    FormWizard.init();
    $('#set_price_date').datepicker();
    var vendor  = $('#vendor_info_id').text(); 
    if(vendor === 'Supplier Information')
    {
        $('.vendor_specific').css('display','block');
    }
    });</script> 
@stop

@section('userscript') 
@include('includes.validators')
<script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/admin/pages/scripts/table-datatables-editable.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js')}}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js')}}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/get_manufacturer_list.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/scripts/approval.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/supplier/supplier_grid_script.js') }}" type="text/javascript"></script>
<!-- <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <script src="/vendor/unisharp/laravel-ckeditor/adapters/jquery.js"></script>--> 
<script type="text/javascript">
    $(document).ready(function () {
    window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
    window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..', okCancelInMulti: false});
    window.asd = $('.SlectBox').SumoSelect({ csvDispCount: 3, captionFormatAllSelected: "Yeah, OK, so everything." });
        
    $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/suppliers/hubslist',
            type: 'GET',                                             
            success: function (rs) 
            {
                var buid = $("#bu_list_id").val();
                $("#hublist1").html(rs);
                $("#hublist1").select2().select2('val',buid);
            }
        });
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/suppliers/gstZoneCode",
            type: "POST",
            dataType: 'json',
            success: function (response) 
            {
                $("#gst_codes").val(response);
            },
            error: function (response) 
            {   
                console.log("ajax call error");
            }
        });
    });

    $("#supplierdocs").validate({
        rules: {
            documentType: {
                required: true
            },
            ref_no: {
                required: true,
                onchange:function(){
                    if($("#documentType option:selected").text() == "GSTIN")
                    {
                       var val = $("#ref_no").val();
                       var re = new RegExp(/^([0][1-9]|[1-2][0-9]|[3][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/i);
                        if (re.test(val) && val.length == 15 ) 
                        {
                            $.ajax({
                                headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                                url: "/suppliers/checkgststatecode/" + val,
                                type: "POST",
                                data: {gst_no: val},
                                dataType: 'json',
                                success: function (response) {
                                    if(response.status)
                                    {
                                        $("#gst_error").text("");
                                        $(":submit").attr("disabled", false);   
                                    }
                                    else
                                    {
                                        $("#gst_error").text("Invalid state code");
                                        $(":submit").attr("disabled", true);
                                    }
                                    
                                },
                                error: function (response) {
                                    $("#gst_error").text("Invalid state code");
                                    $(":submit").attr("disabled", true);
                                }
                            });     
                        } else {
                            $("#gst_error").text("Invalid gstin");
                            $(":submit").attr("disabled", true);
                        }
                    }
                }
            },
            upload_file: {
                required: false,
                extension: "pdf|doc|docx|jpg|jpeg|png"
            }
        },
        submitHandler: function (form) {
            $.ajax({
                headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                url: "/suppliers/supplierdocs",
                type: "POST",
                data: new FormData(form),
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
				beforeSend: function (xhr) {
				    $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);  
				},
				complete: function (jqXHR, textStatus) {
				    $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
				},
                success: function (response) {
                //$('#ajaxResponseDoc').html(response.message);
    				document.getElementById("supplierdocs").reset();
                    if (response.refresh)
                    {
                        $('#supplier_doc_table tbody').html(response.docText);
                        $('#flass_message').text('Saved successfully');                                
                        $('div.alert').show();
                        $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                        $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                        $('html, body').animate({scrollTop: '0px'}, 800);
                    }
                    else
                    {                        
                        $('#supplier_doc_table').append(response.docText);
                        $('#flass_message').text('Saved successfully');                                
                        $('div.alert').show();
                        $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                        $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                        $('html, body').animate({scrollTop: '0px'}, 800);
                    }

                    if (response.count > 0)
                    {                        
                        $('#no_rec_id').css('display', 'none');
                    }
                },
                error: function (response) {
                    $('#ajaxResponseDoc').html('Unable to save Documents');
                }
            });
        }
    });
    
    $(document).on('click', '.tot_product', function() {                
        $('#supplier_tot_grid').igGrid('dataBind'); 
        $("#supplier_tot_grid").css("width","");
        //$("#supplier_tot_grid").igGridSorting('sortColumn', 1, 'ascending');
    });
    
    $(document).on('click', '.grn-del-doc', function () {
    var docId = $(this).attr("id");
    if (confirm('Do you want to delete this document?')) {
    deleteDoc(docId);
    $(this).closest('tr').remove();
	$('#flass_message').text('Document deleted successfully');                                
    $('div.alert').show();
    $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
    $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
    $('html, body').animate({scrollTop: '0px'}, 800); 
    }
    });
    function deleteDoc(id) {
    $.ajax({
    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/suppliers/deletedoc/" + id,
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (response) {
            $('#ajaxResponse').html(response.message);
            },
            error: function (response) {
            $('#ajaxResponse').html('Unable to delete');
            }
    });
    }

var d = new Date();    
$("#license_exp_date").datepicker({
  minDate: 0,
  onSelect: function(d) {
    $("#license_exp_date").datepicker('option', 'minDate', d);
  }
});
$("#insurance_exp_date").datepicker({
  minDate: 0,
  onSelect: function(d) {
    $("#insurance_exp_date").datepicker('option', 'minDate', d);
  }
});
$("#reg_exp_date").datepicker({
  minDate: 0,
  onSelect: function(d) {
    $("#reg_exp_date").datepicker('option', 'minDate', d);
  }
});
$("#fit_exp_date").datepicker({
  minDate: 0,
  onSelect: function(d) {
    $("#fit_exp_date").datepicker('option', 'minDate', d);
  }
});
$("#poll_exp_date").datepicker({
  minDate: 0,
  onSelect: function(d) {
    $("#poll_exp_date").datepicker('option', 'minDate', d);
  }
});
$("#safty_exp_date").datepicker({
  minDate: 0,
  onSelect: function(d) {
    $("#safty_exp_date").datepicker('option', 'minDate', d);
  }
});	
	
	
    $('#start_date').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#end_date').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#payment_days').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#efet_dat').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#insurance_exp_date').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#reg_exp_date').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#license_exp_date').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#fit_exp_date').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#poll_exp_date').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#safty_exp_date').datepicker({ dateFormat: 'yy-mm-dd' });

    addSupplierWarehouseGrid();
    $(document).ready(function(){		
	        $('#approval_form').bootstrapValidator({
            message: 'This value is not valid',
            fields: {               
                approval_comments: {
                    validators: {
                        notEmpty: {
                            message: "Please provide comments"
                        }                       
                    }
                },
                approval_select: {
                    validators: {
                        notEmpty: {
                            message: "Please select status"
                        }
                    }
                },
            }
        }).on('success.form.bv', function (event) {
            event.preventDefault();
            var url = '/suppliers';
            approvalSave(url);
        });	
		
     /*brandDropDown();*/
   

   
  if($('#supplier_approval_hidden').val())
  {
	$('.sumo_po_days').html('<input type="text" class="form-control" id="" value="{{$po_days}}" readonly="readonly">');
    $('.sumo_invoice_days').html('<input type="text" class="form-control" id="" value="{{$invoice_days}}" readonly="readonly">');
    $('.sumo_negotiation').html('<input type="text" class="form-control" id="" value="{{$negotiation}}" readonly="readonly">');
  $('input[type=text]').attr('readonly','true');
    $('select').attr('disabled', true);
    $('.btn-file').css("display", 'none');
    $('.green-meadow').css('display', 'none');
	$(".grn-del-doc").attr('class', 'display');
    }

	if($('#supplier_id').val())
     {
     approval();
	 $('#approval_select_id').attr('disabled', false);
	  $('#approval_select_id').prop("disabled", false);
	
     }	
	
	
    var checkImage = $("#org_supplier_logo").attr('src');
    if (checkImage != '')
    {

    $(".fileinput-preview").removeClass('fileinput-preview fileinput-exists thumbnail').addClass('fileinput-preview thumbnail');
    }
    //getmancatg();
    var csrf_token = $('#csrf-token').val();
    $('#download_template_type').change(function () {
    var template_link = $(this).val();
    var template_type = $('option:selected', this).attr('id');
    $('#download_template_type').css('border', '');
    if (template_type == null){
    template_type = 'TOT';
    $('#download_template_type').css('border', '1px solid red');
    }
    var download_link;
    if (template_type == 'TOT'){
    download_link = '/suppliers/downloadTOTExcel';
    } else if (template_type == 'PIM'){
    download_link = '/suppliers/downloadPIMExcel';
    } else{
    download_link = '/suppliers/downloadTOTPIMExcel';
    }
    $('#download_temp_form').attr('action', download_link);
    $('#import_template_form').attr('action', template_link);
    $('#download_template_button').text('Download ' + template_type + ' Template');
    $('#up_text').text('Upload Your ' + template_type + ' Template');
    });
    $('#upload_pim_file').change(function(){
    var template_type = $('#download_template_type').val();
    if (template_type == ''){
    $('#download_template_type').css('border', '1px solid red');
    $('#import_template_form')[0].reset(); return false;
    } else{
    $('#download_template_type').css('border', '');
    $('#import_template_form').submit();
    }
    });
	
	$('#download_template_button').on('click',function() {
		
		if($('#warehouse_tot').val()==0) {
			
			alert('Please select Warehouse')
			return false;
			
		}
		
	});
	
    $('#import_template_form').submit(function(e){
    e.preventDefault();
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
            $('.product_success_msg').html('');
            $('a[href="#import_messages"]').trigger('click');
            if (data.status_messages.length == 0)
            {
            $('.product_success_msg').append('<tr><td>' + data.message + '</td></tr>');
            } else {


            $.each(data.status_messages, function(key, val){

            $('.product_success_msg').append('<tr><td>' + val + '</td></tr>');
            });
            }
            var supplier_id = $('#supplier_id').val();
            //totGrid(supplier_id);
            $("#supplier_tot_grid").igGrid({"dataSource": '/suppliers/getProducts/' + supplier_id});
            $('#supplier_tot_grid').igGrid('dataBind');			
            },
            cache: false,
            contentType: false,
            processData: false
    });
    });
    $("#upload_pim").on('hide.bs.modal', function () {
    $('#import_template_form')[0].reset();
    });
    });
    function getmancatg()
    {
    $.ajax({
    type: "GET",
            url: "/suppliers/getCatList",
            success: function(result)
            {
            $('#category_id').html(result);
            }
    });
    }
    function sel_selbx(dv_id, sel_val) {
    var options = $(dv_id + ' option');
    $(dv_id + ' option').removeAttr('selected');
    var ind = '';
    $.map(options, function(option) {
    if (option.text == sel_val)
    {
    ind = $(option).index();
    $(dv_id + ' option').eq(ind).prop("selected", "selected")
    }
    if (option.value == sel_val)
    {
    ind = $(option).index();
    $(dv_id + ' option').eq(ind).prop("selected", "selected")
    }
    });
    return;
    }

    function deleteWarehoust(wh_id)
            {            
            if (confirm('Are you sure you want to delete?'))
            {
            token = $("#csrf-token").val();
            $.ajax({
            headers: {'X-CSRF-TOKEN': token},
                    url: '/suppliers/deletewh/' + wh_id,
                    processData: false,
                    contentType: false,
                    success: function (rs)
                    {
                    $("#addSupplierWarehouseGrid").igHierarchicalGrid({"dataSource":'/suppliers/getWarehouseList/' + $("#LpId").val()});
                    //alert(rs);
                    alert("Successfully Deleted.");
                    }
            });
            }}

    function editWarehoust(wh_id)
            {

            token = $("#csrf-token").val();
            $.ajax({
            headers: {'X-CSRF-TOKEN': token},
                    url: '/suppliers/editwh/' + wh_id,
                    processData: false,
                    contentType: false,
                    success: function (rs)
                    {

                    $("#add_wh_label").text("EDIT WAREHOUSE");
                    var test = rs[0].lp_wh_name;
                    $("#wh_name").val(rs[0].sp_wh_name);
                    $("#wh_cont_name").val(rs[0].contact_name);
                    $("#wh_email").val(rs[0].email);
                    $("#wh_phone").val(rs[0].phone_no);
                    $("#wh_address1").val(rs[0].address1);
                    $("#wh_address2").val(rs[0].address2);
                    $("#wh_pincode").val(rs[0].pincode);
                    $("#wh_city").val(rs[0].city);
                    $("#wh_lat").val(rs[0].longitude);
                    $("#wh_log").val(rs[0].latitude);
                    $("#wh_latitude").val(rs[0].latitude);
                    $("#wh_logitude").val(rs[0].longitude);
                    sel_selbx('#wh_state', rs[0].state);
                    sel_selbx('#wh_country', rs[0].country);
                    $("#click_addwh").trigger('click');
                    $("h4.modal-title").replaceWith("<h4 class=" + 'modal-title' + ">EDIT WAREHOUSE</h4>");
                    }
            });
            }

    $('.addwh').on('click', function() {
    $("h4.modal-title").replaceWith("<h4 class=" + 'modal-title' + ">ADD WAREHOUSE</h4>");
    $("#status").val("EDIT");
    });
    $("#click_addwh").on('click', function(e) {
    if (e.hasOwnProperty('originalEvent'))
    {
    $('form[id="submit_form_wh"]')[0].reset();
    $("#status").val("ADD");
    }
    });
    $('.deleteBrand').on('click', function(event) {

    event.preventDefault();
    brand_id = $(this).attr('href');
    var productsCount = $.trim($(this).parents('tr').find('[aria-describedby="brands_grid_table_Products"]').html());
    if (productsCount == 0)
    {

    if (confirm('Are you sure you want to delete?'))
    {
    token = $("#csrf-token").val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: '/suppliers/deletebrand/' + brand_id,
            processData: false,
            contentType: false,
            success: function (rs)
            {
            $("#brands_grid").igHierarchicalGrid({"dataSource":'/suppliers/getBrands/' + $('#legalentity_id').val()});
            }
    });
    }
    }
    else {

    alert('Please delete / unmap products associated with this Brand')

    }
    });
    $(document).on('click', '.deleteProduct', function(event) {

    event.preventDefault();
    product_id = $(this).attr('href');
    if (confirm('Are you sure you want to delete this product?'))
    {
    token = $("#csrf-token").val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: '/suppliers/deleteProduct/' + product_id,
            processData: false,
            contentType: false,
            success: function (rs)
            {            
            //$("#supplier_tot_grid").igHierarchicalGrid({"dataSource":'/suppliers/getProducts/' + $('#supplier_id').val()});
                //$('#supplier_tot_grid').igGrid('dataBind'); 
                $("#supplier_tot_grid").igGridSorting('sortColumn', 1, 'ascending');
                $('#flass_message').text('TOT deleted successfully');
                $('div.alert').show();
                $('div.alert').removeClass('hide');
                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                $('html, body').animate({scrollTop: '0px'}, 800);
          }
    });
    }
    });
    $('.editBrand').on('click', function(e) {

    e.preventDefault();
    token = $("#csrf-token").val();
    brand_id = $(this).attr('href');
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: '/suppliers/editBrand/' + brand_id,
            processData: false,
            contentType: false,
            success: function (rs)
            {


            $("#edit_brand_id").val(rs[0].brand_id);
            $("#brand_name").val(rs[0].brand_name);
            $("#brand_desc").val(rs[0].description);
            $("#brandLogo").parents('.fileinput').find('.thumbnail').html('<img src="/uploads/brand_logos/' + rs[0].logo_url + '"/>')
                    $("#tradeMarkProof").parents('.fileinput').find('.thumbnail').html('<img src="/uploads/brand_trademark_proofs/' + rs[0].trademark_url + '"/>')
                    $("#authorizationProof").parents('.fileinput').find('.thumbnail').html('<img src="/uploads/brand_authorization_proofs/' + rs[0].brand_auth_url + '"/>')

                    /*$("#brandLogo").val(rs[0].logo_url);
                     $("#tradeMarkProof").val(rs[0].trademark_url);
                     $("#authorizationProof").val(rs[0].brand_auth_url);*/

                    $('a[href="#addbrand"]').trigger('click');
            }
  });
    });
        
  $("#set_price_date").keydown(function(e){
    e.preventDefault(); 
  })   
  $(document).on('click', '.set_price', function(e) {    
  
    $('a[href="#setPrice"]').trigger('click');
    $("#set_price_elp").val('');
    $("#set_price_date").val('');    
    $("#set_price_form").data('validator').resetForm();
	$('#set_price_date').datepicker("option", "minDate", new Date());
     $('#set_price_form .has-error').each(function(){
            $(this).removeClass('has-error');
    });
     var product_price_id = $(this).attr('href');
     var token = $("#csrf-token").val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/suppliers/editSetPrice/' + product_price_id,
            type: 'POST',
            success:  function(response) {
               var data     = $.parseJSON(response);
               var supId    = data[0].supp_id;               
               var prod_id  = data[0].prd_id;
                $("#set_price_whid").val(data[0].wh_id);                
                $("#set_supplier_id").val(data[0].supp_name);
                $("#set_product_id").val(data[0].prod_title);
                $("#price_mrp").val(data[0].mrp); 
                
                $("#set_price_productId").val(prod_id);
                $("#set_price_whId").val(data[0].wh_id);
                $("#set_price_supId").val(supId);
               console.log(response);
            }
            
        });
        $(".set_price_whid").attr('disabled',true);
        $("#set_supplier_id").attr('disabled',true);
        $("#set_product_id").attr('disabled',true);
        $("#price_mrp").attr('disabled',true);
      
   });     
   
    $(document).on('click', '.editProduct', function(e) {
    e.preventDefault();        
    $("#brand").attr('disabled',true);    
    $("#tot_product_name").attr('disabled',true);         
    $(".spdt_whid").attr('disabled',true);    
    $("#tot_cat_name").attr('disabled',true);    
    
    $("#add_totprd").text('EDIT PRODUCTS');
    token = $("#csrf-token").val();
    var product_id = $(this).attr('href');
    var supplier_id = 0;
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: '/suppliers/editProduct/' + product_id + '/' + supplier_id,
            processData: false,
            contentType: false,
            success: function (rs)
            {
            if (rs != '') {  
            $('input[name="rtv_allowed"]').removeClass('checked');
            $('input[name="is_preferred_supplier"]').removeClass('checked');
            var inv_type = rs[0].inventory_mode;
            $("#edit_form_product_id").val(rs[0].product_id);
            $("#product_tot_id").val(rs[0].prod_price_id);
            $(".spdt_whid").val(rs[0].le_wh_id);
            $("#tot_brand_name").val(rs[0].brand_name);
            $("#brand").val(rs[0].brand_id);
            $("#tot_cat_name").val(rs[0].category_id);
            $("#tot_product_name").val(rs[0].product_name);
            $("#tot_product_title").val(rs[0].product_title);
            $("#supplier_sku_code").val(rs[0].supplier_sku_code);
            $("#dlp").val(rs[0].dlp);
            $("#distributor_margin").val(rs[0].distributor_margin);
            $("#rlp").val(rs[0].rlp);
            $("#supplier_dc_relationship").val(rs[0].supplier_dc_relationship);
            $("#grn_freshness_percentage").val(rs[0].grn_freshness_percentage);
            $("#tax_type").val(rs[0].tax_type);
            $("#tax").val(rs[0].tax);
            $("#moq").val(rs[0].moq);
            $("#moq_uom").val(rs[0].moq_uom);
            $("#delivery_terms").val(rs[0].delivery_terms);
            $("#delivery_tat_uom").val(rs[0].delivery_tat_uom);
            $("#grn_days").val(rs[0].grn_days);
            //$("#rtv_allowed").val(rs[0].rtv_allowed);
            $("#inventory_mode").val(rs[0].inventory_mode);
            $("#atp").val(rs[0].atp);
            $("#atp_period").val(rs[0].atp_period);
            $(".kvit").val(rs[0].kvi);
            
            //$("#is_preferred_supplier").val(rs[0].is_preferred_supplier);
            $("#efet_dat").val(rs[0].effective_date);
            $('input[name="rtv_allowed"]').prop('checked', false);
            $('input[name="is_preferred_supplier"]').prop('checked', false);
            $('input[name="rtv_allowed"][value="' + rs[0].rtv_allowed + '"]').prop('checked', true);            
            $('input[name="is_preferred_supplier"][value="' + rs[0].is_preferred_supplier + '"]').prop('checked', true);            
            //$('input[name="rtv_allowed"]').removeClass('checked');
            //$('input[name="rtv_allowed"][value="' + rs[0].rtv_allowed + '"]').parent().addClass('checked');
            //$('input[name="is_preferred_supplier"]').removeClass('checked');
            //$('input[name="is_preferred_supplier"][value="' + rs[0].is_preferred_supplier + '"]').parent().addClass('checked');
            $("#tot_margin_type").val(rs[0].is_markup);
            $('a[href="#addp"]').trigger('click');
            
            //grn days selected 
			
            var grn_days =  rs[0].grn_days;
            if(grn_days)
			{				
            var grn_split = grn_days.split(',');
            console.log(grn_split);
            for(var i=0; i<grn_split.length; i++) {                 
                  $('select.sumoTot')[0].sumo.selectItem(grn_split[i]); 
             }
            $('select.sumoTot')[0].sumo.selectItem(grn_split[1]);
            }
            editbrands(rs[0].brand_id, rs[0].product_id, rs[0].category_id, rs[0].cat_name);
            $("#tot_inventory_mode").val(rs[0].inventory_mode);
            }
            }
  });
    });
    function editbrands(brand_id, product_id, category_id, cat_name)
            {
            $.ajax({
            headers: {'X-CSRF-TOKEN': token},
                    url: "/suppliers/productsbybrand",
                    type: "POST",
                    data:{brand: brand_id},
                    success: function (rs) {
                    $('#tot_product_name').empty();
                    $('#tot_product_name').append(rs);
                    $('#tot_product_name').val(product_id);
                    $('#tot_cat_name').empty();
                    $('#tot_cat_name').append($('<option>', {value: category_id, text : cat_name }));                    
                    }
            });
            }


    $('#org_billingaddress_chk').change(function () {
    var org_address1 = $('#org_address1').val();
    var org_address2 = $('#org_address2').val();
    var org_pincode = $('#org_pincode').val();
    var org_state = $('#org_state').val();
    var org_country = $('#org_country').val();
    var org_city = $('#org_city').val();
    if ($('#org_billingaddress_chk').is(':checked')) {
    var st = $('#s2id_org_state').parent().find('.select2-chosen').text();
    var cn = $('#s2id_org_country').parent().find('.select2-chosen').text();
    $('#s2id_org_billingaddress_country').parent().find('.select2-chosen').text($.trim(cn));
     $('#s2id_org_billingaddress_state').parent().find('.select2-chosen').text($.trim(st));
    $('#select2-chosen-6').text($.trim(st));
    $('#org_billingaddress_address1').val(org_address1);
    $('#org_billingaddress_address2').val(org_address2);
    $('#org_billingaddress_pincode').val(org_pincode);
    $('#org_billingaddress_city').val(org_city);
    $('#org_billingaddress_state').val(org_state);
    $('#org_billingaddress_country').val(org_country);
    }
    if (!$('#org_billingaddress_chk').is(':checked')) {
    $('#select2-chosen-6').text('Select State');
    $('#select2-chosen-7').text('Select Country');
    $('#org_billingaddress_address1').val('');
    $('#org_billingaddress_address2').val('');
    $('#org_billingaddress_pincode').val('');
    $('#org_billingaddress_city').val('');
    $('#org_billingaddress_state').val('');
    $('#org_billingaddress_country').val('');
    }
    });
    $('a[href="#tab_22"]').on('click', function () {
    var vendor  = $('#vendor_info_id').text(); 
    if (($('#supplier_id').val()) == '') {
    $('#flass_message').text('Please enter '+vendor);
    $('div.alert').show();
    $('div.alert').removeClass('hide');
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
    $('html, body').animate({scrollTop: '0px'}, 800);
    $('a[href="#tab_11"]').trigger('click');
    return false;
    }
    });
    $('a[href="#tab_55"]').on('click', function () {
            var vendor  = $('#vendor_info_id').text(); 
    if (($('#supplier_id').val()) == '') {
    $('#flass_message').text('Please enter '+vendor);
    $('div.alert').show();
    $('div.alert').removeClass('hide');
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
    $('html, body').animate({scrollTop: '0px'}, 800);
    $('a[href="#tab_11"]').trigger('click');
    return false;
    }
    });
    $('a[href="#tab_44"]').on('click', function () {
            var vendor  = $('#vendor_info_id').text(); 
    if (($('#supplier_id').val()) == '') {
    $('#flass_message').text('Please enter '+vendor);
    $('div.alert').show();
    $('div.alert').removeClass('hide');
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
    $('html, body').animate({scrollTop: '0px'}, 800);
    $('a[href="#tab_11"]').trigger('click');
    return false;
    } else {
    if ($.trim($('#supplier_tot_grid').html()) == '' && $("#legalentity_id").val() != '') {
    totGrid($("#legalentity_id").val());
    }
    }

    })

            $('a[href="#tab_44_1"]').on('click', function () {
        var vendor  = $('#vendor_info_id').text();             
    if (($('#supplier_id').val()) == '') {
   $('#flass_message').text('Please enter '+vendor);
    $('div.alert').show();
    $('div.alert').removeClass('hide');
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
    $('html, body').animate({scrollTop: '0px'}, 800);
    $('a[href="#tab_11"]').trigger('click');
    return false;
    } else {
    if ($("#legalentity_id").val() != '') {
    dcInventoryGrid($("#legalentity_id").val());
    }
    }
    });
    $('a[href="#tab_44_1"]').on('click', function() {
    if ($("#legalentity_id").val() != '') {
    dcInventoryGrid($("#legalentity_id").val());
    }
    });
    $('.manuProductSelect, .spd_whid').on('change', function() {

    var Brand_Id = $('#manufacturer_name').val();
    var Wh_Id = $('#spd_whid').val();
    if (($('#supplier_id').val()) != '')
    {
    if ($.trim(Brand_Id) > 0 && $.trim(Wh_Id) != '')
    {
    ProductsBasedOnBrand(Brand_Id, Wh_Id);
    }
    } else {

    alert('Please enter supplier information');
    $('a[href="#tab_11"]').trigger('click');
    $(".manuProductSelect").val("");
    }
    });
    $('a[href="#tab_33"]').on('click', function () {
    if (($('#supplier_id').val()) == '') {
    $('#flass_message').text('Please enter supplier information');
    $('div.alert').show();
    $('div.alert').removeClass('hide');
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
    $('html, body').animate({scrollTop: '0px'}, 800);
    $('a[href="#tab_11"]').trigger('click');
    return false;
    } else {
    if ($.trim($('#brands_grid').html()) == '' && '<?php echo Session::get('legalentity_id') ?>' != '') {
    //brandsGrid(<?php echo Session::get('legalentity_id'); ?>);
    }
    }
    });
    $('a[href="#addbrand"]').on('click', function(e) {

    brand_validation_var.resetForm();
    $('#add_brand_form .has-error').each(function(){

    $(this).removeClass('has-error');
    });
    if (e.originalEvent !== undefined)
    {
    $('input[name="brand_logo"]').rules('add', 'required');
    $('#add_brand_form')[0].reset();
    $('#edit_brand_id').val('');
    $("#brandLogo").parents('.fileinput').find('.thumbnail').html('')
            $("#tradeMarkProof").parents('.fileinput').find('.thumbnail').html('')
            $("#authorizationProof").parents('.fileinput').find('.thumbnail').html('')
    }
    else{
    $('input[name="brand_logo"]').rules('remove', 'required');
    }
    })

    function totSumoReset() {
            var tot = [];
            $('#grn_days option:selected').each(function () {
            tot.push($(this).index());
            });            
            for (var i = 0; i < tot.length; i++) {
            $('.sumoTot')[0].sumo.unSelectItem(tot[i]);
            }
       }
    $('a[href="#addp"]').on('click', function(e) {
    //supplier_product_formwizard.resetForm();
    //totSumoReset();
	$('select.sumoTot')[0].sumo.unSelectAll();
    $('#supplier_add_products .has-error').each(function(){

    $(this).removeClass('has-error');
    });
    if (e.originalEvent !== undefined)
    {
	$('select.sumoTot')[0].sumo.unSelectAll();	
    $('#supplier_add_products')[0].reset();
    $("#add_totprd").text("ADD PRODUCTS");
    $('#edit_form_product_id').val('');    
    $("#brand").attr('disabled',false);        
    $("#tot_product_name").attr('disabled',false);         
    $(".spdt_whid").attr('disabled',false);    
    $("#tot_cat_name").attr('disabled',false); 
    }

    })

            $('#app_supp_info').on('click', function(e){
    var token = $("#csrf-token").val();
    var name = $("#app_supp_info").attr("name");
    //alert("---"+name);
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/approve",
            type: "POST",
            data: name,
            success: function (rs) {
            window.location.replace("/suppliers");
            alert('sucessfully approved.');
            }
    })
    })

            $('#sup_comments_id').on('click', function(e){
    var token = $("#csrf-token").val();
    var name = $("textarea#comments_area").val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/reject",
            type: "POST",
            data: name,
            success: function (rs) {
            $('.close').trigger('click');
            window.location.replace("/suppliers");
            alert('supplier has been rejected.');
            }
    })
    })

            $('#brand').change(function () {
    var token = $("#csrf-token").val();
    var brand = $('#brand').val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/productsbybrand",
            type: "POST",
            data:{brand: brand},
            success: function (rs) {
            $('#tot_product_name').empty();
            $('#tot_product_name').append(rs);            
            }
    });
    });
    $('#tot_product_name').change(function () {
    var token = $("#csrf-token").val();
    var product_id = $('#tot_product_name').val();
    var prod_name = $("#tot_product_name option:selected").text();
    $('#prod_name').val(prod_name);
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/categoriesbyproducts",
            type: "POST",
            data:{product_id: product_id},
            success: function (rs) {
            $('#tot_cat_name').empty();
            $('#tot_cat_name').append(rs);            
            }
    });
    });
    $('#tot_product_name').change(function () {
    var token = $("#csrf-token").val();
    var product_id = $('#tot_product_name').val();
    var brand = $('#brand').val();
    var category = $('#tot_cat_name').val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/productotdetails",
            type: "POST",
            data:{product_id: product_id, brand: brand, category: category},
            success: function (data) {            
            $.each(data, function(idx, obj) {
            //alert(obj.cbp);
            //$("#edit_form_product_id").val(obj.product_id);
            $("#tot_product_title").val(obj.product_title);
            $("#tot_brand_name").val(obj.brand_name);
            $("#tot_cat_name").val(obj.category_id);
            $("#tot_product_name").val(obj.product_id);
            $("#tot_product_title").val(obj.product_title);
            $("#tot_product_desc").val(obj.short_description);
            $("#tot_sku_id").val(obj.seller_sku);
            $("#tot_ean").val(obj.upc);
            $("#tot_mrp").val(obj.mrp);
            $("#tot_base_price").val(obj.base_price);
            $("#tot_msp").val(obj.msp);
            $("#tot_vat").val(obj.vat);
            $("#tot_cst").val(obj.cst);
            $("#tot_vat").val(obj.vat);
            $("#tot_rbp").val(obj.rlp);
            $("#tot_ebp").val(obj.dlp);
            $("#tot_cbp").val(obj.cbp);
            $('#tot_credit_days').val(obj.credit_days);
            $("#tot_location_type").val(obj.return_location_type);
            $("#tot_delivery_terms").val(obj.delivery_terms);
            $('input[name="return_accepted"]').removeClass('checked')
                    $('input[name="return_accepted"][value="' + obj.is_return_accepted + '"]').attr('checked', true);
            $('input[name="return_accepted"][value="' + obj.is_return_accepted + '"]').parent().addClass('checked');
            $("#tot_margin_type").val(obj.is_markup);
            $("#tot_inventory_mode").val(obj.inventory_mode);
            });
            }
    });
    });
    /* $( '#supplier_add_products' ).find( 'input, select, textarea, radio' ).click(function() {
     var brand = $('#brand').val();
     var product_id = $('#tot_product_name').val();
     var categoty_id = $('#tot_cat_name').val();
     if(brand == '')
     {
     alert('Please Slect Brand');
     }
     if(brand != '' && product_id =='')
     {
     alert('Please Slect Product');
     }
     if(brand != '' && product_id !='')
     {
     alert('Please Slect Category');
     }
     
     });*/
    $("#logistic_picode").blur(function ()
    {
    var token = $("#csrf-token").val();
    var pincode = $("#logistic_picode").val();
    if (pincode.length == 6)
    {
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/googlepincode/" + pincode,
            type: "GET",
            success: function (data) {

            //success data
            var country1 = '';
            var country = '';
            var state = '';
            var city = '';
            var data = jQuery.parseJSON(data);
            var address_components = data['results'][0]['address_components'];
            var types = address_components;
            var my_city = '';
            var my_postalcode;
            var my_dist = '';
            var my_state;
            var my_country;
            $.each(types, function(idx, obj) {
            if (obj.types[0] == "postal_code")
            {
            my_postalcode = obj.long_name;
            }
            if (obj.types[0] == "locality" && obj.types[1] == "political")
            {
            my_city = obj.long_name;
            }
            if (obj.types[0] == "administrative_area_level_2" && obj.types[1] == "political")
            {
            my_dist = obj.long_name;
            }
            if (obj.types[0] == "administrative_area_level_1" && obj.types[1] == "political")
            {
            my_state = obj.long_name;
            }
            if (obj.types[0] == "country" && obj.types[1] == "political")
            {
            my_country = obj.long_name;
            }
            });
            if (my_city.length != 0)
            {
            $("#logistic_city").val(my_city);
            }
            else
            {
            $("#logistic_city").val(my_dist);
            }
            sel_selbx('#logistic_state', $.trim(my_state));
            sel_selbx('#logistic_country', $.trim(my_country));
            $('.spinnerQueue').hide();
            }
    });
    }
    });
    $("#org_pincode").blur(function ()
    {
    var token = $("#csrf-token").val();
    var pincode = $("#org_pincode").val();
    if (pincode.length == 6)
    {
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/googlepincode/" + pincode,
            type: "GET",
            beforeSend: function() {                
               $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);
            },
            success: function (data) {

            //success data
            var country1 = '';
            var country = '';
            var state = '';
            var city = '';
            var data = jQuery.parseJSON(data);
            var address_components = data['results'][0]['address_components'];
            var types = address_components;
            var my_city = '';
            var my_postalcode;
            var my_dist = '';
            var my_state;
            var my_country;
            $.each(types, function(idx, obj) {
            if (obj.types[0] == "postal_code")
            {
            my_postalcode = obj.long_name;
            }
            if (obj.types[0] == "locality" && obj.types[1] == "political")
            {
            my_city = obj.long_name;
            }
            if (obj.types[0] == "administrative_area_level_2" && obj.types[1] == "political")
            {
            my_dist = obj.long_name;
            }
            if (obj.types[0] == "administrative_area_level_1" && obj.types[1] == "political")
            {
            my_state = obj.long_name;
            }
            if (obj.types[0] == "country" && obj.types[1] == "political")
            {
            my_country = obj.long_name;
            }
            });
            if (my_city.length != 0)
            {
            $("#org_city").val(my_city);
            }
            else
            {
            $("#org_city").val(my_dist);
            }
            $('#select2-chosen-4').text($.trim(my_state));
            $('#s2id_org_country').parent().find('.select2-chosen').text($.trim(my_country));
            sel_selbx('#org_state', $.trim(my_state));
            sel_selbx('#org_country', $.trim(my_country));
            $('.spinnerQueue').hide();
            }
    });
    }
    });
    $("#wh_pincode").blur(function ()
    {
    var token = $("#csrf-token").val();
    var pincode = $("#wh_pincode").val();
    if (pincode.length == 6)
    {
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/googlepincode/" + pincode,
            type: "GET",
            success: function (data) {

            //success data
            var country1 = '';
            var country = '';
            var state = '';
            var city = '';
            var data = jQuery.parseJSON(data);
            var address_components = data['results'][0]['address_components'];
            var types = address_components;
            var my_city = '';
            var my_postalcode;
            var my_dist = '';
            var my_state;
            var my_country;
            $.each(types, function(idx, obj) {
            if (obj.types[0] == "postal_code")
            {
            my_postalcode = obj.long_name;
            }
            if (obj.types[0] == "locality" && obj.types[1] == "political")
            {
            my_city = obj.long_name;
            }
            if (obj.types[0] == "administrative_area_level_2" && obj.types[1] == "political")
            {
            my_dist = obj.long_name;
            }
            if (obj.types[0] == "administrative_area_level_1" && obj.types[1] == "political")
            {
            my_state = obj.long_name;
            }
            if (obj.types[0] == "country" && obj.types[1] == "political")
            {
            my_country = obj.long_name;
            }
            });
            if (my_city.length != 0)
            {
            $("#wh_city").val(my_city);
            }
            else
            {
            $("#wh_city").val(my_dist);
            }
            sel_selbx('#wh_state', $.trim(my_state));
            sel_selbx('#wh_country', $.trim(my_country));
            $('.spinnerQueue').hide();
            }
    });
    }
    });
    $("#org_billingaddress_pincode").blur(function ()
    {
    var token = $("#csrf-token").val();
    var pincode = $("#org_billingaddress_pincode").val();
    if (pincode.length == 6)
    {
    if (pincode.length !== 6)
    {
    $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load', true);
    return;
    }
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/googlepincode/" + pincode,
            type: "GET",
            beforeSend: function() {                
                $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);
            },
            success: function (data) {
            //success data
            var country1 = '';
            var country = '';
            var state = '';
            var city = '';
            var data = jQuery.parseJSON(data);
            var address_components = data['results'][0]['address_components'];
            var types = address_components;
            var my_city = '';
            var my_postalcode;
            var my_dist = '';
            var my_state;
            var my_country;
            $.each(types, function(idx, obj) {
            if (obj.types[0] == "postal_code")
            {
            my_postalcode = obj.long_name;
            }
            if (obj.types[0] == "locality" && obj.types[1] == "political")
            {
            my_city = obj.long_name;
            }
            if (obj.types[0] == "administrative_area_level_2" && obj.types[1] == "political")
            {
            my_dist = obj.long_name;
            }
            if (obj.types[0] == "administrative_area_level_1" && obj.types[1] == "political")
            {
            my_state = obj.long_name;
            }
            if (obj.types[0] == "country" && obj.types[1] == "political")
            {
            my_country = obj.long_name;
            }
            });
            if (my_city.length != 0)
            {
            $("#org_billingaddress_city").val(my_city);
            }
            else
            {
            $("#org_billingaddress_city").val(my_dist);
            }
            $('#select2-chosen-6').text($.trim(my_state));
            $('#s2id_org_billingaddress_country').parent().find('.select2-chosen').text($.trim(my_country));
            sel_selbx('#org_billingaddress_state', $.trim(my_state));
            sel_selbx('#org_billingaddress_country', $.trim(my_country));
            $('.spinnerQueue').hide();
            }
    });
    }
   });
    $("#warehouse_id").change(function ()
    {
    var wh_id = $("#warehouse_id").val();
    $("#wh_id").val(wh_id);
    });
    $(document).on('click', '.import_save', function (event) {
    event.preventDefault();
    var wh_id = $('#wh_id').val();
    if (wh_id == '')
    {
    alert('Please select a Warehouse.');
    return false;
    }

    var formData = new FormData($("#submit_formm")[0]);
    $.ajax({
    url:'/suppliers/importDcMappingExcel/' + wh_id,
            data:formData,
            async:false,
            type:'post',
            processData: false,
            contentType: false,
            success:function(response){
            alert(response);
            },
    });
    });
    $("#savebrands").click(function (){
    $('a[href="#tab_44"]').trigger('click');
    });
    $("#cancelbrands").click(function (){
    e.preventDefault();
    window.location = "/suppliers";
    });
    /*function getinventorymode() {
     var token  = $("#csrf-token").val();
     $.ajax({
     headers: {'X-CSRF-TOKEN': token},
     url: "/suppliers/getinventorymode",
     type: "POST",
     success: function (rs) {
     $('#tot_inventory_mode').empty();
     $('#tot_inventory_mode').append(rs);
     console.log(rs);
     }  
     });
     };*/
    $("#download_dcmapping_form").submit(function(event) {
    var wh_id = $("#warehouse_id").val();
    if (wh_id == '')
    {
    alert('Select Warehouse');
    return false;
    }
    var prod_ids = $("#totproducts").val();
    var res = prod_ids.replace('["', '');
    var res = res.replace('"]', '');
    var res = res.replace('"', '');
    var suffix = res.match(/\d+/);
    var intRegex = /^\d+$/;
    var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
    if (intRegex.test(suffix) || floatRegex.test(suffix)) {

    }
    else
    {
    alert('Select Atleaset One Product From Grid');
    return false;
    }

    });
    $(document).on('click', '.import_save_wh', function (event) {
    event.preventDefault();
    var legalentity_id = $('#wh_legalentity_id').val();
    if (legalentity_id == '')
    {
    alert('Please select a Supplier or create new Supplier.');
    }

    //var lp_id = 1;
    var formData = new FormData($("#submit_formm_wh")[0]);
    $.ajax({
    url:'/suppliers/importExcel/' + legalentity_id,
            data:formData,
            async:false,
            type:'post',
            processData: false,
            contentType: false,
            success:function(response){
            alert(response);
            },
    });
    });
    $('input[type=radio][name=rtv]').change(function() {
    if (this.value == '1') {
    $("#rtv_scope").prop("disabled", false);
    $("#rtv_location").prop("disabled", false);
    $("#rtv_timeline").prop("disabled", false);
    }
    else if (this.value == '0') {
    $("#rtv_scope").prop("disabled", true);
    $("#rtv_location").prop("disabled", true);
    $("#rtv_timeline").prop("disabled", true);
    }
    });
    $(document).on('click', '.enableDisableProducttot', function() {
    var DcId = $('#spd_whid').val(); 
    var supplier_id = $("#supplier_id").val();
    var type = "disable";
    var flag = '';
    if ($(this).is(":checked") === true) {
    flag = 1;
    } else {
    flag = 0;
    }


    var token = $("#csrf-token").val();
    var ProductId = $(this).attr('data_attr_productid');
    //alert(ProductId);

    if (ProductId != '') {
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/totmapping",
            type: "POST",
            data: {ProductId:ProductId, flag:flag, DcId:DcId, supplier_id:supplier_id},
            success: function () {
            //$("#product_choose_grid").igGrid("dataBind");
            //$("#supplier_tot_grid").igGrid("dataBind");
            }
    })


    }

    });
    $(".pincode").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== - 1 ||
            // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
            }
            });
    $("#driver_contact_old").change(function () {
        $("#add_new_user").attr("disabled",true);
        var supplierId = $(this).val();
        if(supplierId != "") {
            $.ajax({
                method: "GET",
                url: "/get/suppliers/"+supplierId
            }).done(function( driver ) {
                var data=driver[0];
                console.log(driver.user_roles_id);
                console.log(driver['user_roles_id']);
                $("#org_firstname").val(data.firstname).attr("disabled",true);
                $("#org_lastname").val(data.lastname).attr("disabled",true);
                $("#org_email").val(data.email_id).attr("disabled",true);
                $("#org_mobile").val(data.mobile_no).attr("disabled",true);
                $("#org_landline").val(data.landline_no).attr("disabled",true);
                $("#org_extnumber").val(data.landline_ext).attr("disabled",true);
                $("#license_no").val(data.driving_license_no).attr("disabled",true);
                $("#license_exp_date").val(data.license_exp_date).attr("disabled",true);
                $("#org_address1").val(data.address1).attr("disabled",true);
                $("#org_address2").val(data.address2).attr("disabled",true);
                $("#org_country").select2('val',data.country).attr("disabled",true);
                $("#org_state").select2('val',data.state_id).attr("disabled",true);
                $("#org_city").val(data.city).attr("disabled",true);
                $("#org_pincode").val(data.pincode).attr("disabled",true);
            });
        }
    });
    

    $("#add_new_user").click(function(){
        $("#org_firstname").val("");
        $("#org_lastname").val("");
        $("#org_email").val("");
        $("#org_mobile").val("");
        $("#org_landline").val("");
        $("#org_extnumber").val("");
        $("#org_address1").val("");
        $("#org_address2").val("");
        $("#org_country").val("");
        $("#org_state").val("");
        $("#org_city").val("");
        $("#org_pincode").val("");
        $("#license_no").val("");
        $("#license_exp_date").val("");
        $("#driver_contact_old").attr("disabled",true);
    });
</script> 
@stop
@extends('layouts.footer')
