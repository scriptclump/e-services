@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<ul class="page-breadcrumb breadcrumb">
    <li><a href="/">{{trans('dashboard.dashboard_heads.heads_home')}}</a><i class="fa fa-angle-right" aria-hidden="true"></i>
    </li>
    <li><a href="/products/all">{{trans('products.titles.products')}}</a><i class="fa fa-angle-right" aria-hidden="true"></i>
    </li><li><span class="bread-color">{{trans('products.titles.offerproducts')}}</span></i></li>
    
</ul>
<span id="success_message_ajax"></span>
<div class="alert alert-success hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>            

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Offer Products </div>
                <div class="actions"> 
                     

                    <a href="#tag_2" data-toggle="modal" id = "" class="btn green-meadow">ADD SKU Products</a>

                </div>

                <div class="col-md-12 text-right">

                </div>
            </div>
    
            <div class="portlet-body">
                
                <table id="mustskuproductsGrid"></table>
                
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

<div class="modal modal-scroll fade in" id="tag_2" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="modalpopupclose" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Add SKU Product</h4>
            </div>
            <div class="modal-body">
                <form id="must_sku_form" action="/products/addmustskuproduct">
                      <input type="hidden" name="_token" id = "csrf-token" value="{{ Session::token() }}">                      
                        <div class="row">
                            <div class="col-md-12 " ><!-- align="pull-left" -->
                                
                                    <select id="dcid" name="dcid" class="form-control select2me">
                                        <option value="0">Select Warehouse</option>
                                        @foreach($dcs as $dc)
                                        @if($dc->lp_wh_name!='')
                                        <option value="{{ $dc->le_wh_id}}"> {{ $dc->display_name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                               
                            </div>
                            <br/>
                            <div class="col-md-12 " ><!-- align="pull-left" -->
                                
                                  <input type="text" name="product_name_or_sku_code" id="product_name_or_sku_code" class="form-control" placeholder="Product Name or SKU Code">
                                  <input type="hidden" id="addproduct_id" name="addproduct_id" class="form-control" placeholder="SKU,Product Name" />
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 " align="center"> 
                                <button type="submit" id="download_template_button" role="button" class="btn green-meadow">ADD</button>
                                <br/>
                            </div>
                        </div>
                    </form>
                    <br>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<a class="btn green-meadow" data-toggle="modal" style="display:none" href="#import_success_messages">Show errors</a>

<div id="import_success_messages" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
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
<script src="{{ URL::asset('assets/admin/pages/scripts/product/mustsku_grid_script.js') }}" type="text/javascript"></script>
<script>
$(document).attr("title", "{{trans('dashboard.dashboard_title.company_name')}} - {{trans('products.titles.offerproducts')}}");
    function deleteskuproduct(pid,le_wh_id){
        if (confirm('Are you sure you want to delete product?'))
        {
            token = $("#csrf-token").val();
            $.ajax({
            headers: {'X-CSRF-TOKEN': token},
                    url: '/products/deleteskuProduct' ,
                    type: "GET",
                    data: {
                           product_id: pid,
                           le_wh_id:le_wh_id,
                       },
                    dataType:'json',
                    success: function (rs) {
                        if(rs.status_messages == true) { 
                            $('#flass_message').text('Your product is deleted successfully');
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                            $('div.alert').not('.alert-important').delay(5000).fadeOut("350");
                            $('html, body').animate({scrollTop: '0px'}, 800);
                           $("#mustskuproductsGrid").igGrid("dataBind");
                        } else {
                            $('#flass_message').text('Failed to delete product ');
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                            $('div.alert').not('.alert-important').delay(5000).fadeOut("350");
                            $('html, body').animate({scrollTop: '0px'}, 800);                    
                        }
                   }
                });
        }
    }

    function changeskuproductstatus(pid,le_wh_id){
        
        token = $("#csrf-token").val();
        var status=$('#status_'+le_wh_id+'_'+pid).is(':checked');
        if(status)
        {
            var decission = confirm("Are you sure you want to Active the SKU");
            isChecked = '1';
        }else{
            var decission = confirm("Are you sure you want to In-Active the SKU");
            isChecked = '0';
        }
        //event.preventDefault();
        if (decission == true) {
            $.ajax({
            headers: {'X-CSRF-TOKEN': token},
                    url: '/products/changeskuProductstatus' ,
                    type: "GET",
                    data:{
                      product_id:pid,
                      le_wh_id:le_wh_id,
                      status:isChecked,  
                    }, 
                    dataType:'json',
                    success: function (rs) {
                        console.log(rs.product_name);
                        if(rs.status_messages == 1) { 
                            $('#flass_message').text(rs.product_name+' status changed successfully');
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                            $('div.alert').not('.alert-important').delay(5000).fadeOut("350");
                            $('html, body').animate({scrollTop: '0px'}, 800);
                           $("#mustskuproductsGrid").igGrid("dataBind");
                           //$("#mustskuproductsGrid").igGrid({dataSource: '/products/getmustskuProducts'}).igGrid("dataBind");
                        } else {
                            $('#flass_message').text(rs.product_name+ 'failed to update product status');
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                            $('div.alert').not('.alert-important').delay(5000).fadeOut("350");
                            $('html, body').animate({scrollTop: '0px'}, 800);
                            $("#mustskuproductsGrid").igGrid("dataBind");                    
                        }
                   }
                });
        }else{
            $("#mustskuproductsGrid").igGrid("dataBind");
        }
    }
    
</script>
@stop
@section('style')
.ui-iggrid .ui-iggrid-filtercell .ui-igedit{width: auto !important; height:50px !important;}
@stop
@extends('layouts.footer')