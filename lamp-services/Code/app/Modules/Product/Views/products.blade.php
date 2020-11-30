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
                <div class="actions"> 
                    <div class="btn  form-group" href="/createproduct"><select class="form-control select2me" name="product_wh" id="product_wh" style="width:200px">
                            @if(!empty($wh_list))
                            @if($access_alldcs)                             
                            <option value="0">All</option>
                            @endif
                                @foreach($wh_list as $key=>$value)
                                  @if(isset($wh_id) && $key== $wh_id)
                                        <option value="{{$key}}" selected >{{$value}}</option>
                                   @else
                                    <option value='{{$key}}'>{{$value}}</option>
                                   @endif 
                                @endforeach
                            @endif
                        </select></div>     
                    @if($createProductFeature)
                        <a class="btn green-meadow" href="createproduct">{{trans('products.lables.create')}}</a>
                    @endif

                    
                     @if($allProductFeature || $uploadProductFeature || $importexportFeature || $warehouseFeature || $exportAllProductElpFeature)
                        <a class="btn green-meadow" href="/products/product_config" >Products Configuration</a>
                    @endif

                    @if($allProductFeature || $uploadProductFeature || $importexportFeature || $warehouseFeature || $exportAllProductElpFeature)
                        <a class="btn green-meadow" href="/products/product_color_config" >Product Color Configurations</a>
                    @endif

                    <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span> </div>
                <div class="col-md-12 text-right">

                </div>
            </div>
			<div class="col-md-12 text-right" style="font-size:11px"><b>* All Amounts in </b><i class="fa fa-inr" aria-hidden="true"></i></div>
                @if($showFilterData && Session::get('warehouseId')!=0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="caption">
                            <span class="caption-subject bold font-blue"> Filter By :</span>
                            <span class="caption-helper sorting1">
                                <a href="{{$app['url']->to('/')}}/products/creation" class="{{($status == 'creation') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Creation">Creation (<span id="creation_drafted" class='supp_cnt'>{{$creation+$drafted}}</span>)</a>&nbsp;
                                <a href="{{$app['url']->to('/')}}/products/approval" class="{{($status == 'approval') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Approval">Approval (<span id="approval" class='ser_pro_list_grid'>{{$approval}}</span>)</a>&nbsp;
                                <a href="{{$app['url']->to('/')}}/products/filling" class="{{($status == 'filling') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Filling">Filling (<span id="filling" class='veh_list_grid'>{{$filling}}</span>)</a>&nbsp;
                                <a href="{{$app['url']->to('/')}}/products/enablement" class="{{($status == 'enablement') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Enablement">Enablement (<span id="verification" class='veh_pro_list_grid'>{{$verification}}</span>)</a>&nbsp;
                            </span>

                        </div>
                    </div>
                </div>    
                <div class="row">
                    <div class="col-md-12">
                        <div class="caption status">
                            <span class="caption-subject bold font-blue uppercase"></span>
                            <span class="caption-helper sorting1">
                                <a href="{{$app['url']->to('/')}}/products/open" class="{{($status == 'open') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Open">Open (<span id="open" class='supp_cnt'>{{$open+$drafted}}</span>)</a>&nbsp;
                                <a href="{{$app['url']->to('/')}}/products/disabled" class="{{($status == 'disabled') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Disabled">Disabled (<span id="disabled" class='ser_pro_list_grid'>{{$disabled}}</span>)</a>&nbsp;
                                <a href="{{$app['url']->to('/')}}/products/active" class="{{($status == 'active') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Active">Active (<span id="active" class='veh_list_grid'>{{$active}}</span>)</a>&nbsp;
                                <a href="{{$app['url']->to('/')}}/products/all" class="{{($status == 'all') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="All">All (<span id="all" class='veh_pro_list_grid'>{{$all}}</span>)</a>&nbsp;
                            </span>

                        </div>
                    </div>
                </div>
                @endif
    
            <div class="portlet-body">
				@if($extendedGrid)
				<table id="extendedProductsGrid"></table>	
                
				@else
				<table id="productsGrid"></table>
				@endif
            </div>
        </div>
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

<div class="addEditPriceSection">
    <input type="hidden" name="product_id" id="product_id" value="">
    @include('Pricing::addEditPriceSection')
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
.centerAlign { text-align:center;}
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
<script src="{{ URL::asset('assets/admin/pages/scripts/product/products_grid_script.js') }}" type="text/javascript"></script>

<!-- <script src="{{ URL::asset('assets/global/plugins/get-brands-dropdown.js') }}" type="text/javascript"></script> -->
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/priceModel.js') }}" type="text/javascript"></script>
<script>

    $(function () {
    $("#productsGrid_Supplier_Count").attr("title", "Number Of Suppliers");
    $("#productsGrid_Images").attr("title", "Product With Images");
    $("#productsGrid_Schemes").attr("title", "Schmes");
    });
    var csrf_token = $('#csrf-token').val();
    
    $(document).on('click', '.deleteProduct', function(event) {

    event.preventDefault();
    product_id = $(this).attr('href');   
    if (confirm('Are you sure, you want to delete product. It will delete TOT, Pack configuration, Slab rates?'))
    {
    token = $("#csrf-token").val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: '/product/deleteProduct' ,
            type: "GET",
            data: 'product_id=' + product_id,
            processData: false,
            contentType: false,
            success: function (rs) {
                if(rs == 1) { 
                    $('#flass_message').text('Your product is deleted successfully');
                    $('div.alert').show();
                    $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                    $('div.alert').not('.alert-important').delay(5000).fadeOut("350");
                    $('html, body').animate({scrollTop: '0px'}, 800);
                   $("#productsGrid").igGrid("dataBind");
                } else {
                    $('#flass_message').text('Product cannot be deleted as it is associated with Orders/PO/Indent/Inward transactions');
                    $('div.alert').show();
                    $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                    $('div.alert').not('.alert-important').delay(5000).fadeOut("350");
                    $('html, body').animate({scrollTop: '0px'}, 800);                    
                }
           }
        });
       }
    });
// needed for Price Update -- Do not delete (for price) //
var updatePriceID = 0;
var globalPageFlag = 2;
// ========================================//
// call Price Model from Product (for price)
function savePriceDataFromPrice(productID){
    $('#product_id').val(productID);
    $('#save_price').modal('toggle');
}


 $("#product_wh").change(function() {
    var csrf_token = $('#csrf-token').val();
    var product_wh = $('#product_wh').val();
    var referrer = $(this).attr('href');
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrf_token},
        url: '/products/wh',
        type: 'POST',
        data: {'referrer': referrer,'product_wh':product_wh},
        dataType: 'json',
        async: false,
        success: function (data) {
			window.location = '/products/all';
        }
    
        });    
//$("#productsGrid").igGrid("dataBind");
//$("#extendedProductsGrid").igGrid("dataBind");
    
});
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

$(document).ready(function() {
    var csrf_token = $('#csrf-token').val();
    var product_wh = $('#product_wh').val();
    if(product_wh!=0){
   $.ajax({
    headers: {'X-CSRF-TOKEN': csrf_token},
        url: '/products/counts',
        type: 'POST',
        data: {'product_wh':product_wh},
        dataType: 'json',
        async: false,
        success: function (data) {
            console.log(data);
            var drafted=0;
            var creation=0;
            var approval=0;
            var filling=0;
            var verification=0;
            var open=0;
            var disabled=0;
            var active=0;
   /*         jQuery.each( data, function( i, val ) {
                if(data[i].status==57001){
                     drafted=data[i].count;
                     //console.log(drafted);
                }else if(data[i].status==57002){
                     creation=data[i].count;
                     //console.log(creation);
                }else if(data[i].status==57007){
                     approval=data[i].count;
                    // console.log(approval);
                }else if(data[i].status==57003){
                     filling=data[i].count;
                     //console.log(filling);
                }else if(data[i].status==57006){
                     verification=data[i].count;
                     //console.log(verification);
                }
});*/
            drafted=data['Drafted'];
            creation=data['Creation'];
            approval=data['Approval'];
            filling=data['Filling'];
            verification=data['Enablement'];
            var disabled=0;
            var all=0;
            var creation=parseInt(drafted)+parseInt(creation);
            $('#creation_drafted').html(creation);
            $('#approval').html(approval);
            $('#filling').html(filling);
            $('#verification').html(verification);
           open=data['open'];
          open=parseInt(open)+parseInt(drafted);
          $('#open').html(open);
          disabled=data['disabled'];
          disabled=parseInt(disabled);
          $('#disabled').html(disabled);
          all=data['all'];
          all=parseInt(all);
          $('#all').html(all);
          active=data['active'];
          active=parseInt(active);
          $('#active').html(active);

        }
   });
}
 });
</script>
@stop
@section('style')
.ui-iggrid .ui-iggrid-filtercell .ui-igedit{width: auto !important; height:50px !important;}
@stop
@extends('layouts.footer')
