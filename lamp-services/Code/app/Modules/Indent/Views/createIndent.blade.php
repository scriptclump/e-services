@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/indents">Indents</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Create Indent</li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> CREATE INDENT </div>
                <div class="tools">&nbsp;</div>
            </div>
            <div class="portlet-body bodyheight">
                <div id="ajaxResponse" style="display:none;" class="alert alert-danger"></div>
                <form action="/indents/createIndentAction" id="createIndentForm">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Indent Date</label>
                                <div class="input-icon right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="hidden" name="_token" id="csrf_token" value="{{ csrf_token() }}">
                                    <input type="text" class="form-control" name="indent_date" id="indent_date" value="{{date('m/d/Y')}}" placeholder="Indent Date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label">Warehouse</label>
                                <div id="warehouse_list">
                                    <select name="indent_warehouse" id="indent_warehouse" class="form-control select2me">
                                        <option value="">Select Warehouse</option>
                                        @foreach($warehouses as $warehouse)
                                    <option value="{{$warehouse->le_wh_id}}">{{$warehouse->lp_wh_name}}, {{$warehouse->address1}}, {{$warehouse->city}}, {{$warehouse->pincode}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label">Supplier</label>
                                <div id="supplier_list">
                                    <select name="indent_supplier" id="indent_supplier" class="form-control select2me">
                                        <option value="">Select Supplier</option>

                                    </select>
                                </div>
                            </div>
                        </div>                        
                    </div>
                 
                    


                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn green-meadow margbot" id="adskubtn" href="#add_sku" disabled="" role="button" data-toggle="modal"> ADD SKU </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                        <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#000">
                               
                                <table class="table table-striped table-bordered table-advance table-hover" id="indent_product_table">
                                    <thead>
                                        <tr>
                                            <th width="10%" class="txtCenter">SKU Code</th>
                                            <th width="20%">Product Name</th>
                                            <th width="10%" class="txtCenter">EAN</th>
                                            <th width="10%" class="txtCenter">MRP (Rs.)</th>
                                            <th width="10%" class="txtCenter">Avail. Inventory</th>
                                            <th width="10%" class="txtCenter">Indent Qty</th>
                                            <th width="10%" class="txtCenter">MBQ</th>
                                            <th width="10%" class="txtCenter">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($indentProducts)>0)
                                            @foreach($indentProducts as $indentProduct)
                                            <?php echo $indentProduct[0] ?>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:60px;">
                        <hr />
                        <div class="col-md-12 text-center"> 
                            <button type="submit" id="saveSkubtn" class="btn green-meadow">Save</button>
                            <a href="/indents/index" class="btn green-meadow">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- ADD SKU Model Start-->
<div id="add_sku" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">ADD SKU</h4>
                </div>
                <div class="modal-body">
                    <form id="addSkuForm">
                        
                        <div class="row margtop">

                            <div class="col-md-12">
                                <div style="display:none;" id="error-msg" class="alert alert-danger"></div>
                                <div id="product_list">
                                    <select class="form-control select2me" name="sup_skus" id="sup_skus" required="required">
                                        <option value="">Select Product</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row margtop">
                            <div class="col-md-12">
                                <input type="number" min="1" maxlength="4" class="form-control" placeholder="Indent Qty" name="identQty" id="identQty"/>                                
                            </div>
                        </div>

                        <div class="modal-footer margtop">
                        <div class="row">
                            <div class="col-md-12 text-center" >
                                <button class="green-meadow btn" type="submit" id="addSku">ADD</button>
                            </div>
                        </div>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
<!-- ADD SKU Model End-->


@stop

@section('style')
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.theme.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.css" rel="stylesheet" />
<style type="text/css">
 <!--.txtCenter {text-align: center !important;}-->
 .error {color:red;}
 .margtop{ margin-top: 15px;}
 .margbot{ margin-bottom: 15px;}
 .bodyheight{height:553px;}

 .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
.loderholder img{ position: absolute; top:50%;left:50%;    }
    </style>
@stop
@section('script')
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
@stop
@section('userscript')
<script type="text/javascript">
    var csrf_token = $('#csrf_token').val();
    $('#indent_date').datepicker();
    function getCheckedBox() {
        var checked = false;
        $("input[name='indent_products[]']").each(function () {
            if ($(this).prop('checked') == true) {
                checked = true;
                return;
            }
        });
        return checked;
    }
    function checkProductAdded(product_id) {
        var checked = true;
        $("input[name='indent_products[]']").each(function () {
            var productid_exist=$(this).val();
            if (productid_exist == product_id) {
                checked = false;
                return;
            }
        });
        return checked;
    }
    $(document).ready(function () {
        $(".modal").on('hide.bs.modal', function () {
            var form_id = $(this).find('form').attr('id');
            $('#' + form_id)[0].reset();
        });
        $("#createIndentForm").validate({
            rules: {
                indent_date: {
                    required: true
                },
                indent_supplier: {
                    required: true
                },
                indent_warehouse: {
                    required: true
                },
            },
            submitHandler: function (form) {
                var form = $('#createIndentForm');
                if (getCheckedBox()) {
                    //$('#saveSkubtn').attr('disabled', true);
                    $('.loderholder').show();
                    $.ajax({
                        url: form[0].action,
                        type: form[0].method,
                        data: form.serialize(),
                        dataType: 'json',
                        success: function (data) {
                            if (data.status == 200) {
                                $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                                window.setTimeout(function(){window.location.href = '/indents/detail/' + data.indent_id},2000);
                            } else {
                                $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html(data.message).show();
                                //$('#saveSkubtn').attr('disabled', false);
                                $('.loderholder').hide();
                            }
                        },
                        error: function (response) {
                            $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html("{{Lang::get('salesorders.errorInputData')}}");
                            //$('#saveSkubtn').attr('disabled', false);
                            $('.loderholder').hide();
                        }
                    });
                } else {
                    $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html("{{Lang::get('indent.alertEmptyProd')}}").show();
                }
            }
        });
        $("#addSkuForm").validate({
            rules: {
                sup_brands: {
                    required: true
                },
                sup_skus: {
                    required: true
                },
                identQty: {
                    required: true
                },
            },
            submitHandler: function (form) {
                var form = $('#addSkuForm');
                $('#saveSkubtn').removeAttr('disabled');
                var product_id = $('#sup_skus').val();
                var le_wh_id = $('#indent_warehouse').val();
                var supplier_id = $('#indent_supplier').val();
                if(checkProductAdded(product_id)){
                    $('#addSku').attr('disabled', true);
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': csrf_token},
                        url: '/indents/getProductInfo?le_wh_id='+le_wh_id+'&supplier_id='+supplier_id,
                        type: 'POST',
                        data: form.serialize(),
                        dataType:'JSON',
                        success: function (data) {
                           $('#indent_product_table').append(data.product_data);
                           $('.close').click();
                           $('#sup_skus').select2('val','');
                           $('#addSku').attr('disabled', false);
                        },
                        error: function (response) {

                        }
                    });
                }else{
                    $('#error-msg').html("{{Lang::get('indent.alertAddedProd')}}").show();
                   // window.setTimeout(function(){$('#error-msg').hide()},2000);
                }
            }
        });
        $('#indent_warehouse').change(function(){
            var le_wh_id = $(this).val();
            $('#adskubtn').attr('disabled',true);
            $.ajax({
                headers: {'X-CSRF-TOKEN': csrf_token},
                url: '/indents/supplierSupplierOptions',
                type: 'POST',
                data: {le_wh_id:le_wh_id},
                dataType:'JSON',
                success: function (data) {
                   $('#supplier_list').html(data.suppliers);
                   $("#indent_supplier").select2().select2('val','');
                },
                error: function (response) {
                    
                }
            });
        });
        $(document).on('change','#indent_supplier',function(){
            var supplier_id = $(this).val();
            var warehouse_id = $('#indent_warehouse').val();
            if(supplier_id!='' && warehouse_id!=''){
                $.ajax({
                    headers: {'X-CSRF-TOKEN': csrf_token},
                    url: '/indents/productsBySupplier',
                    type: 'POST',
                    data: {supplier_id:supplier_id,warehouse_id:warehouse_id},
                    dataType:'JSON',
                    success: function (data) {
                       $('#product_list').html(data.products);
                       $('#sup_skus').select2();
                       $('#adskubtn').attr('disabled',false);
                    },
                    error: function (response) {
                        $('#adskubtn').attr('disabled',true);
                    }
                });
            }else{
                $('#adskubtn').attr('disabled',true);
            }
        });
        $(document).on('click','.delete_product',function(){      
            var product_id = $(this).attr('data-id');
            $.ajax({
                headers: {'X-CSRF-TOKEN': csrf_token},
                url: '/indents/removeProducts',
                type: 'POST',
                data: {product_id:product_id},
                dataType:'JSON',
                success: function (data) {
                   
                },
                error: function (response) {
                    
                }
            });
            $(this).closest('tr').remove();
            return false;            
        });
    });
</script>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop
