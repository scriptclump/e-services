@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')



<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">                                
                <input type="hidden" value="<?php print_R($parent_id); ?>" id="parent_id" />
                <div class="caption"> EDIT PRODUCTS LIST </div>
                <!--<div class="actions"> <a class="btn green-meadow" href="createproduct">Create Product</a><a class="btn green-meadow" data-toggle="modal" href="#upload_pim">Upload Product Templates</a><span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question-circle-o"></i></a></span> </div>-->                
            </div>
            <br/>
            <div class="alert alert-success hide">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <span id="flass_message"></span>
            </div>
            
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Brand</label>
                       
                         <select class="form-control get_brand_id select2me" id="brand_id" name="brand_id">
                        
                    </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Products</label>
                        <input type="hidden" id="getBrandId" value="">
                        <select class="form-control get_brand_id select2me" id="get_products">
                        </select>                        
                        <input type="hidden" id="related_product_id"> 
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group" >
                        <label>&nbsp;</label>
                        <?php if(isset($add_product) && $add_product == 1) { ?>
                        <button type="button" class="btn btn-primary" id="add_related_product"> Add</button>
                        <?php }?>
                    </div>
                </div>
            </div>


            <div class="portlet-body">
                <table id="productsListGrid"></table>
            </div>
        </div>
    </div>
</div>

{{HTML::style('css/switch-custom.css')}}

@stop

<style>
    .ui-iggrid-filterrow{display:none !important;}
</style>

@section('userscript')
@include('includes.ignite')
@include('includes.group_repo')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/products-drop-down.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/get-brands-dropdown.js') }}" type="text/javascript"></script>
<script>

    $(function () {
        var parent_id = $("#parent_id").val();
        childproductlist(parent_id);//grid loading
       
        $("#add_related_product").click(function () {
            var token = $("#csrf-token").val();
            var parent_id = $("#parent_id").val();
            var product_id = $("#get_products").val();
            var brand_id = $("#brand_id").val();
            
            if(brand_id == 0) {
                $('#flass_message').text("Please Select The Brand  Name...");
                $('div.alert').show();
                $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                $('html, body').animate({scrollTop: '0px'}, 800);                
             return false;   
            }
            if(product_id == 0) {
                $('#flass_message').text("Please select the product");
                $('div.alert').show();
                $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                $('html, body').animate({scrollTop: '0px'}, 800);                
             return false;   
            }            
            var commentsData = {parent_id: parent_id, product_id: product_id};
            if (product_id != '' && product_id != null  && product_id!=0) {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: '/createRelativeProducts',
                    async: false,
                    type: 'POST',
                    data: commentsData,
                    success: function (rs) {
                        if(rs == 1) {
                            $('#flass_message').text("Product already exist in the list / childs assigned to it");
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');;
                            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                            $('html, body').animate({scrollTop: '0px'}, 800);                                                        
                        } else {
                            $('#flass_message').text("You have add product successfully ");
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                            $('html, body').animate({scrollTop: '0px'}, 800);                            
                            $("#productsListGrid").igGrid("dataBind");
                            
                            $("#brand_id").select2('val', '0');
                            $("#get_products").select2('val', '0');
                            $("#get_products").load( "/getProductsList/0?parent_id=1" );
                       }
                    },
                    error: function (err) {
                        console.log('Error: ' + err);
                    }
                });
            } else {
                $('#flass_message').text("Please Select The Brand  Name...");
                $('div.alert').show();
                $('div.alert').removeClass('hide');
                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                $('html, body').animate({scrollTop: '0px'}, 800);                
            }
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
            var is_sellable     = $(this).attr('data_is_sellable');            
            if( cp_flag == 1) { 
                if (cpPricing == '' || tax_val == '' || is_sellable == '0') {
                    $('#flass_message').text('Tax/Pricing/is Sellable  not available for this product ' + ' ( ' + product_name + ' )');
                    $('div.alert').show();
                    $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                    $('div.alert').not('.alert-important').delay(5000).fadeOut("slow");
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
                            //$('#productsListGrid').igGrid('dataBind');
                        }
                    }
                })
            }
        });

        $(document).on('click', '.deletechild',function() {            
            var chaildId = $(this).attr('data_attr_chaild_id');
            var productname = $(this).attr('data_attr_productName');
            var parentId = $("#parent_id").val();
            var token    = $("#csrf-token").val();
            var conFirm  =  confirm('Are you sure you want to delete this product (' + productname + ') ?');
            if(conFirm == true) {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                        url: '/productlist/deleteChildproducts',                    
                        type: 'POST',
                        data: 'parentId='+parentId+'&chaildId='+chaildId,
                        success: function(response) {
                            $('#flass_message').text('You have successfully deleted this product (' +  productname + ')' );
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                            $('html, body').animate({scrollTop: '0px'}, 800);
                            $("#productsListGrid").igGrid("dataBind");
                            
                            $("#brand_id").select2('val', '0');
                            $("#get_products").select2('val', '0');
                            $("#get_products").load( "/getProductsList/0?parent_id=1" );
                        }
                }); 
             }
         });        
    });

    function productRepo(product_repo) {
        var curr_parent_id = product_repo;
        var prev_parent_id = $("#parent_id").val();
        $.ajax({
            url: '/productlist/updateGroupRepo',
            type: 'GET',
            data: 'curr_parent_id=' + curr_parent_id + '&prev_parent_id=' + prev_parent_id,
            success: function (response) {
                if (response == 1) {
                    $('#flass_message').text("Child product is changed to parent");
                    $('div.alert').show();
                    $('div.alert').removeClass('hide');
                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                    $('html, body').animate({scrollTop: '0px'}, 800);
                    window.setTimeout(function () {
                        window.location.href = '/productlist/index';
                    }, 2000);                    
                }

            }

        })
    }
</script>
@stop
@extends('layouts.footer')