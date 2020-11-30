@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message_ajax"></span>
<div class="alert alert-success hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>
<?php 
$drafted = $creation = $approval = $filling = $verification = $openCount = $disableCount = $activeCount = $allCount = 0;
/*$le_counts = json_decode(json_encode($le_counts),1);
foreach($le_counts as $counts)
{
    if(isset($counts['status']) && $counts['status'] == 57001)
    {
        $drafted = $counts['count'];
    }
    if(isset($counts['status']) && $counts['status'] == 57002)
    {
        $creation = $counts['count'];
    }
    if(isset($counts['status']) && $counts['status'] == 57007)
    {
        $approval = $counts['count'];
    }
    if(isset($counts['status']) && $counts['status'] == 57003)
    {
        $filling = $counts['count'];
    }
    if(isset($counts['status']) && $counts['status'] == 57006)
    {
        $verification = $counts['count'];
    }
}
$open = (isset($le_counts['open'][0]))? $le_counts['open'][0]:0;
$disabled = (isset($le_counts['disabled'][0]))? $le_counts['disabled'][0]:0;
$active = (isset($le_counts['active'][0]))? $le_counts['active'][0]:0;
$all = (isset($le_counts['all'][0]))? $le_counts['all'][0]:0;*/
$open=$disabled=$active=$all=0;
?>            

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> {{trans('products.lables.mange')}} </div>
                
                
            </div>
        </div>
    </div>
    <br/><br/><br/><br/>
    <div class="col-md-12 col-sm-12">
                    
                    @if($uploadProductFeature)
                    <div class="col-md-3">
                        <a class="btn green-meadow" data-toggle="modal" href="#upload_pim">{{trans('products.lables.upload')}}</a> </div>
                    @endif
                  
                    
                    @if($allProductFeature)
                    <div class="col-md-3">
                        <a class="btn green-meadow" href="/products/downloadAllProductInfo" >All Products Download link</a> </div>
                    @endif
                    
                    @if($importexportFeature)   
                    <div class="col-md-3">     
                    <a class="btn green-meadow" data-toggle="modal" href="#upload_product_pack_config">Import/ Export Product pack Config</a> </div> @endif
                    
                    @if($warehouseFeature)
                     <div class="col-md-3">
                    <a class="btn green-meadow" data-toggle="modal" href="#upload_warehouse_config">{{trans('products.lables.warehouse_upload')}}</a> </div>@endif
                    
                    <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span> </div>
    </div>
    <br/><br/>
    <div class="col-md-12 col-sm-12">

        <div class="col-md-3">
            @if($cp_Enable_Sheet == 1)
            <a href="#tag_2" data-toggle="modal" id = "" class="btn green-meadow">Import Product CP</a>
            @endif
        </div>
        
        @if($exportAllProductElpFeature)
            <div class="col-md-3">     
            <a class="btn green-meadow" data-toggle="modal" href="#exportallproductelps" >Export All Products ELP's </a> </div>
        @endif
        <div class="col-md-3">
            @if($import_prd_cust_esu == 1)
               <a href="#tag_3" data-toggle="modal" id = "" class="btn green-meadow">Import Customer Type Esu</a>
            @endif
        </div>

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


<div id="upload_pim" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">{{trans('products.lables.upload_product')}}</h4>
            </div>
            <div class="modal-body">
                <br>
                <form id="download_temp_form" action="/products/downloadPIMExcel">
                    <div class="row">
                        <div class="col-md-12 " align="center">
                            <select class="form-control select2me" id="brand_id" name="brand_id">

                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12 " align="center">
                            <select class="form-control select2me" name="category_id" id="category">
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12 " align="pull-left">
                            <input type="checkbox" name="with_data"/> {{trans('products.lables.with_data')}}
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12 " align="center">
                            <p id="down_err_msg" style="color:#d65b60; display: none;">Please select brand and category.</p> 
                            <button type="submit" id="product_download_template_button" role="button" class="btn green-meadow">
                                {{trans('products.lables.download_pim')}}</button>
                            <p class="topmarg"></p>
                            
                        </div>
                    </div>
                </form>
                <br>
                <div class="row">
                    <div class="col-md-12 text-center" align="center">
                        <form id='import_template_form' action="{{ URL::to('/products/importPIMExcel') }}" class="text-center" method="post" enctype="multipart/form-data">
                            <div class="fileUpload btn green-meadow"> <span id="up_text">{{trans('products.lables.upload_pim')}}</span>
                                <input type="file" class="form-control upload" name="import_file" id="upload_pim_file"/>
                            </div>
                            <span class="loader" id="pimloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>
                            <br/><br/>
                            <p class="topmarg"></p>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="upload_warehouse_config" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">{{trans('products.lables.upload_wh_config')}}</h4>
            </div>
            <div class="modal-body">
                <br>
                <form id="download_wh_form" action="/products/downloadwhexcel">
                    <div class="row">
                        <div class="col-md-12 " align="center">
                            <select class="form-control select2me" id="wh_list_id" name="wh_list_id">
                            </select>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-12 " align="pull-left">
                            <input type="checkbox" name="with_data"/> {{trans('products.lables.with_data')}}
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12 " align="center"> 
                            <button type="submit" id="download_template_button" role="button" class="btn green-meadow">
                                {{trans('products.lables.download_wh_config_btn')}}</button>
                            <p class="topmarg"></p>
                        </div>
                    </div>
                </form>
                <br>
                <div class="row">
                    <div class="col-md-12 text-center" align="center">
                        <form id='import_template_form2' action="{{ URL::to('/products/importWhExcel') }}" class="text-center" method="post" enctype="multipart/form-data">
                            <div class="fileUpload btn green-meadow"> <span id="up_text">{{trans('products.lables.upload_wh_config_btn')}}</span>
                                <input type="file" class="form-control upload" name="import_file" id="upload_wh_file"/>
                            </div>
                            <span class="loader" id="pimloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>
                            <br/><br/>
                            <p class="topmarg"></p>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="upload_product_pack_config" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                 <h4 class="modal-title">Import/ Export Product Pack Config</h4>
            </div>
             <div class="modal-body">
                <br>
                <form id="download_pack_excel" action="/products/donwloadPackConfigExcel">
                    <br>                   
                    <div class="row">
                        <div class="col-md-12 " align="center"> 
                            <button id="pack_excel_ip" role="button" class="btn green-meadow">
                                Download All Product Pack Config Template</button>
                            <p class="topmarg"></p>
                        </div>
                    </div>
                </form>
                <br>
                <div class="row">
                    <div class="col-md-12 text-center" align="center">
                        <form id='import_template_form3' action="{{ URL::to('/products/uploadPackConfigExcel') }}" class="text-center" method="post" enctype="multipart/form-data">
                            <div class="fileUpload btn green-meadow"> <span id="up_text"> Upload Product Pack Config Template</span>
                                <input type="file" class="form-control upload" name="import_file" id="upload_pack_config_file"/>
                            </div>
                            <span class="loader" id="packloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>
                            <br/><br/>
                            <p class="topmarg"></p>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal modal-scroll fade in" id="tag_2" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="modalpopupclose" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">CP-ESU Upload</h4>
            </div>
            <div class="modal-body">
                <form id="download_temp_form" action="/products/downloadCPEnableExcel">
                       
                      
                        <div class="row">
                            <div class="col-md-12 " align="pull-left">
                                <input type="checkbox" name="with_data"/> with data
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 " align="center"> 
                                <button type="submit" id="download_template_button" role="button" class="btn green-meadow">Download CP Enable/ESU Template</button>
                                <br/>
                                <p class="topmarg">Check With data to update product information</p>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="row">
                        <div class="col-md-12 text-center" align="center">
                            <form id='import_cpenable_template_form' action="{{ URL::to('/products/uploadCPEnableExcelSheet') }}" class="text-center" method="post" enctype="multipart/form-data">
                                <div class="fileUpload btn green-meadow"> <span id="up_text">Upload CP Enable/ESU Template</span>
                                    <input type="file" class="form-control upload" name="import_file" id="upload_cpenable_file"/>
                                </div>
                                <span class="loader" id="dcloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>
                            <br/>
                                <p class="topmarg">Upload the filled product template</p>

                            </form>
                                                    </div>
                    </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<a class="btn green-meadow" data-toggle="modal" style="display:none" href="#import_cp_messages">Show errors</a>

<div id="import_cp_messages" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
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

<div class="modal modal-scroll fade in" id="exportallproductelps" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="modalpopupclose" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Export All Product Elp's</h4>
            </div>
            <div class="modal-body">
                <div class="alert" role="alert" id="modalAlert" style="margin:-20px;"></div>
                <form id="download_all_products_elp" action="/products/exportallproductelps">
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="all_product_elp_fdate" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="all_product_elp_tdate" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: default current month data will download
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="downloadfile" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                            </form>
                                                    </div>
                    </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal modal-scroll fade in" id="tag_3" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="modalpopupclose" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Customer Type ESU Upload</h4>
            </div>
            <div class="modal-body">
                <form id="download_temp_form" action="/products/downloadEsuExcel">
                       <div class="row">
                            <div class="col-md-12 " align="pull-left">
                                <input type="checkbox" name="with_data"/> with data
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 " align="center"> 
                                <button type="submit" id="download_template_button" role="button" class="btn green-meadow">Download Customer Type ESU Template</button>
                                <br/>
                                <p class="topmarg">Check With data to update product esu information</p>
                            </div>
                        </div>
                </form>
                <br>
                    <div class="row">
                        <div class="col-md-12 text-center" align="center">
                            <form id='import_esu_template_form' action="{{ URL::to('/products/uploadEsuExcel') }}" class="text-center" method="post" enctype="multipart/form-data">
                                <div class="fileUpload btn green-meadow"> <span id="up_text">Upload Customer Type ESU Template</span>
                                    <input type="file" class="form-control upload" name="import_file" id="upload_esu_file"/>
                                </div>
                                <span class="loader" id="dcloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>
                <br/>
                                <p class="topmarg">Upload the filled product template</p>

                            </form>
                        </div>
                    </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

{{HTML::style('css/switch-custom.css')}}

<style type="text/css">
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
</style>


 
@stop
@section('style')
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript')
@include('includes.ignite')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/getcategorylist.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/get-brands-dropdown.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<!-- <script src="{{ URL::asset('assets/admin/pages/scripts/price/priceModel.js') }}" type="text/javascript"></script> -->
<script>
$(document).ready(function() {
    $(function () {
    $("#productsGrid_Supplier_Count").attr("title", "Number Of Suppliers");
    $("#productsGrid_Images").attr("title", "Product With Images");
    $("#productsGrid_Schemes").attr("title", "Schmes");
    });
    var csrf_token = $('#csrf-token').val();
    $('#upload_pim_file').change(function(){
    $('#import_template_form').submit();
    });

    $('#exportallproductelps').on('hide.bs.modal', function () {
        $("#all_product_elp_fdate").datepicker('setDate', null);
        $("#all_product_elp_tdate").datepicker('setDate', null);
    });

    $("#modalpopupclose").click(function(){
        $("#modalAlert").hide();
        $('#modalAlert').data('bs.modal',null);
    });

    $('#import_template_form2').submit(function(e){
    e.preventDefault();
    var formData = new FormData($(this)[0]);
    $('#pimloader').show();
    var url = $(this).attr('action');
    $('.product_success_msg').html('');
    $.ajax({
    headers: {'X-CSRF-TOKEN': csrf_token},
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (data) {
            $('.close').trigger('click');
            $('#pimloader').hide();
            $('a[href="#import_messages"]').trigger('click');
            data = jQuery.parseJSON(data);
            if (data.status_messages.length == 0)
            {
            $('.product_success_msg').append('<tr><td>' + data.message + '</td></tr>');
            } else {


            $.each(data.status_messages, function(key, val){

            $('.product_success_msg').append('<tr><td>' + val + '</td></tr>');
            });
            }
            //$('.product_success_msg').append('<tr><td>' + 'Warehouse configuration saved sucessfully' + '</td></tr>');
            //alert();
            },
            cache: false,
            contentType: false,
            processData: false
    });
    });
    /*$("#pack_excel_ip").click(function(e){
        e.preventDefault();
        var pack_type = $("#pack_type option:selected").val();
        if(pack_type != 0)
        {
            $('#download_pack_excel').submit();
        }else
        {
            alert("Please select pack type.");            
        }
    });*/
    $('#import_template_form3').submit(function(e){
    e.preventDefault();
    var formData = new FormData($(this)[0]);
    $('#packloader').show();
    var url = $(this).attr('action');
    $('.product_success_msg').html('');
    $.ajax({
    headers: {'X-CSRF-TOKEN': csrf_token},
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (data) {
                alert(data);

            $('.close').trigger('click');
            $('#packloader').hide();
            /*$('a[href="#import_messages"]').trigger('click');
            data = jQuery.parseJSON(data);*/
          /*  if (data.status_messages.length == 0)
            {
            $('.product_success_msg').append('<tr><td>' + data.message + '</td></tr>');
            } else {


            $.each(data.status_messages, function(key, val){

            $('.product_success_msg').append('<tr><td>' + val + '</td></tr>');
            });
            }
*/            //$('.product_success_msg').append('<tr><td>' + 'Warehouse configuration saved sucessfully' + '</td></tr>');
            //alert();
            },
            cache: false,
            contentType: false,
            processData: false
    });
    });    
    
    


    
    $('#upload_wh_file').change(function(){
    $('#import_template_form2').submit();
    });
    $('#upload_pack_config_file').change(function(){
    $('#import_template_form3').submit();
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
            data = jQuery.parseJSON(data);
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
            //$('.product_success_msg').html(data.status_messages);
            var supplier_id = $('#supplier_id').val();
            $('#productsGrid').igHierarchicalGrid({dataSource: 'products/getProducts'});
            },
            cache: false,
            contentType: false,
            processData: false
    });
    });
    $("#upload_pim").on('hide.bs.modal', function () {
    $('#import_template_form')[0].reset();
    });
    
    $("#upload_warehouse_config").on('hide.bs.modal', function () {
    $('#import_template_form2')[0].reset();
    });
    $("#upload_product_pack_config").on('hide.bs.modal', function () {
    $('#import_template_form3')[0].reset();
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
    getmancatg();
// needed for Price Update -- Do not delete (for price) //
var updatePriceID = 0;
var globalPageFlag = 2;
// ========================================//
// call Price Model from Product (for price)
function savePriceDataFromPrice(productID){
    $('#product_id').val(productID);
    $('#save_price').modal('toggle');
}

$("#product_download_template_button").click(function(e){
    e.preventDefault();
    $("#down_err_msg").hide();
    var brand = $("#brand_id").val();
    var cat = $("#category").val();
    if(brand == 0 && cat == 0 || brand == null && cat == null || brand == 0 || brand == null || cat == 0 || cat == null)
    {
       $("#down_err_msg").show();
    }else{
       $("#download_temp_form").submit();
    }
});
 });

$('#upload_cpenable_file').change(function(){
    /*var template_type = $('#download_template_type').val();
    if (template_type == ''){
    $('#download_template_type').css('border', '1px solid red');
    $('#import_template_form')[0].reset(); return false;
    } else{*/
    $('#import_cpenable_template_form').css('border', '');
    $('#import_cpenable_template_form').submit();
   // }
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
            $('.product_success_msg').html('');
            $('a[href="#import_cp_messages"]').trigger('click');
            console.log(data);
            /*if (data.status_messages.length == 0)
            {*/
            $('.product_success_msg').append('<tr><td>' + data.message + '</td></tr>');
            //} else {

            $('.product_success_msg').append(data.status_messages);     
            },
            cache: false,
            contentType: false,
            processData: false
    });
    });

    $('#all_product_elp_fdate').datepicker({
        dateFormat: 'dd-mm-yy',
        maxDate:0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#all_product_elp_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
     $('#all_product_elp_tdate').datepicker({
        dateFormat: 'dd-mm-yy',
        maxDate:'+0D',
    });

    $('#upload_esu_file').change(function(){
        $('#import_esu_template_form').css('border', '');
        $('#import_esu_template_form').submit();
        $('#upload_esu_file').val('');    
    });
    $('#import_esu_template_form').submit(function(e){
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
                $('.product_success_msg').html('');
                $('a[href="#import_cp_messages"]').trigger('click');
                console.log(data);
                $('.product_success_msg').append('<tr><td>' + data.message + '</td></tr>');
                $('.product_success_msg').append(data.status_messages);     
                },
                cache: false,
                contentType: false,
                processData: false
        });
    });

</script>
@stop
@section('style')
.ui-iggrid .ui-iggrid-filtercell .ui-igedit{width: auto !important; height:50px !important;}
@stop
@extends('layouts.footer')