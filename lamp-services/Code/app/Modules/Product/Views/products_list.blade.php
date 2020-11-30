@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="alert alert-success hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Manage Products </div>
                <!--<div class="actions"> <a class="btn green-meadow" href="createproduct">Create Product</a><a class="btn green-meadow" data-toggle="modal" href="#upload_pim">Upload Product Templates</a><span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question-circle-o"></i></a></span> </div>-->                
            </div>
            <div class="portlet-body">
                <table id="productsListGrid"></table>
            </div>
        </div>
    </div>
</div>

{{HTML::style('css/switch-custom.css')}}
<style>
    .cpenabled {
        margin-left: 25px !important;
    }
</style>

@stop

@section('userscript')
<script src="{{ URL::asset('assets/admin/pages/scripts/product/product_list_grid.js') }}" type="text/javascript"></script>
@include('includes.ignite')
@include('includes.group_repo')
<script>
    $(function () {
        productList();
        $(document).on('click', '.enableDisableProduct', function () {
            var product_name = $(this).attr('name');
            var flag = '';
            if ($(this).is(":checked") === true) {
                flag = 1;
            } else {
                flag = 0;
            }
            
            var cp_process          = '';
            var productId           = $(this).attr('data_attr_productid');
            var supplierId          = $(this).attr('data_attr_supplierId');
            var cpPricing           = $(this).attr('value');    
            var tax_val             = $(this).attr('data_attr_tax');  
            var with_priceing       = $(this).attr('data_without_priceing');  
            var is_sellable         = $(this).attr('data_is_sellable');              
            if(flag == 1) { 
                if (cpPricing == '' || tax_val == '' || is_sellable == '0') {                    
                    $('#flass_message').text('Tax/Pricing/is Sellable  not available for this group ' + ' ( ' + product_name + ' ) ');
                    $('div.alert').show();
                    $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                    $('div.alert').not('.alert-important').delay(5000).fadeOut("350");
                    $('html, body').animate({scrollTop: '0px'}, 800);
                    return false;
                }
             }

            var token = $("#csrf-token").val();
            if (productId != '') {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: '/productlist/cpStatus',
                    type: 'POST',
                    data: {ProductId: productId, flag: flag},
                    success: function (response) {
                        if (response) {
                            cp_process = 1;
                        }
                    }
                });
            }

            $.confirm({
                confirm: function () {
                    if (productId != '') {
                        //flag = 1;
                        $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            url: '/productlist/cpChildStatus',
                            type: 'POST',
                            data: {ProductId: productId, flag: flag},
                            success: function (response) {
                                if (flag == 1) {
                                    //$('#flass_message').text('CP enabled for only child products which has pricing, tax and is Sellable configured (' + with_priceing  + ')');
                                    $('#flass_message').text('CP enable for all products under this group (' + with_priceing  + ')');
                                } else {
                                    $('#flass_message').text('CP disabled for all products under this group ' + ' ( ' + product_name + ' ) ');
                                }
                                $('div.alert').show();
                                $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                                $('div.alert').not('.alert-important').delay(7000).fadeOut("slow");
                                $('html, body').animate({scrollTop: '0px'}, 800);
                                $('#productsListGrid').igGrid('dataBind');
                            }
                        })
                    }
                },
                cancel: function () {
                    if (flag == 1) {
                        $('#flass_message').text('CP enable for this product ' + ' ( ' + product_name + ' ) ');
                    } else {
                        $('#flass_message').text('CP disabled for this product ' + ' ( ' + product_name + ' ) ');
                    }
                    $('div.alert').show();
                    $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                    $('div.alert').not('.alert-important').delay(5000).fadeOut("450");                    
                    $('html, body').animate({scrollTop: '0px'}, 800);
                    $('#productsListGrid').igGrid('dataBind');
                },
                confirmButton: "Yes",
                cancelButton: "No"
            });

        });

        $(document).on('click', '.cp_enabled', function () {
            var product_name = $(this).attr('name')
            var cp_flag = '';
            if ($(this).is(":checked") === true) {
                cp_flag = 1;
            } else {
                cp_flag = 0;
            }            
            var productId       = $(this).attr('data_attr_productid');
            var cpPricing       = $(this).attr('value');
            var supplierId      = $(this).attr('data_attr_supplierId');  
            var tax_val         = $(this).attr('data_attr_tax');     
            var parent_index    = $(this).attr('data_parent');
            var is_sellable     = $(this).attr('data_is_sellable');            
            if( cp_flag == 1) { 
                if (cpPricing == '' || tax_val == '' || is_sellable == '0') {
                    $('#flass_message').text('Tax/Pricing/is Sellable  not available for this product ' + ' ( ' + product_name + ' )');
                    $('div.alert').show();
                    $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                    $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                    $('html, body').animate({scrollTop: '0px'}, 800);
                    return false;
                }
            }
            var token = $("#csrf-token").val();
            if (productId != '') {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: '/productlist/cpStatus',
                    type: 'POST',
                    data: {ProductId: productId, flag: cp_flag},
                    success: function (response) {
                        if (response) {
                            if (cp_flag == 1) {
                                $('#flass_message').text('CP enable for this product' + ' ( ' + product_name + ' ) ');
                            } else {
                                $('#flass_message').text('CP disabled for this product' + ' ( ' + product_name + ' ) ');
                            }
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                            $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                            $('html, body').animate({scrollTop: '0px'}, 800);
                            if(parent_index == 0 ){
                               $('#productsListGrid').igGrid('dataBind');
                             }
                        }
                    }
                })
            }
        });

    })
</script>
@stop
@extends('layouts.footer')