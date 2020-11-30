@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')

<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/indents/index">Indents</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Indent Detail</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Indent Details # {{$indentArr[0]->indent_code}}</div>
                
                <!-- <div class="actions">                    
                    <a href="/indents/print/{{$indentArr[0]->indent_id}}" class="btn green-meadow"><i class="fa fa-print"></i></a>
                    <a href="/indents/pdf/{{$indentArr[0]->indent_id}}" class="btn green-meadow"><i class="fa fa-download"></i></a>
                    <a href="/indents/autoindent" class="btn green-meadow">Auto Indent</a>
                </div> -->

                <div class="row">
                    <div class="col-md-12 text-right">
                        <p class="notific">* <b>All Amounts in</b> <i class="fa fa-inr"></i></p>
                    </div>    
                </div> 
            </div>
<!--             <div class="row" id="ajaxResponse" style="display:none;">
                <div class="col-md-12">
                    <div class="alert alert-success" id="ajaxMsg"></div>
                </div>
            </div> -->
            <span id="ajaxResponse"></span>

            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-4 col-sm-12">        
                        <div class="portlet ">            
                            <h4>Indent Details</h4>
                            <div class="box2">
                                <div class="row static-info">
                                    <div class="col-md-5 name"> Indent ID: </div>
                                    <div class="col-md-7 value"> {{$indentArr[0]->indent_code}}</div>
                                </div>                      
                                <div class="row static-info">
                                    <div class="col-md-5 name"> Indent Date: </div>
                                    <div class="col-md-7 value"> {{date('d-m-Y', strtotime($indentArr[0]->indent_date))}} </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name"> Indent Type: </div>
                                    <div class="col-md-7 value"> {{($indentArr[0]->indent_type == 1 ? 'Manual' : 'Auto')}} </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12">        
                        <div class="portlet ">            
                            <h4>DC Location</h4>
                            <div class="box2">

                                @if(is_object($warehouse))
                                <p>{{$warehouse->lp_wh_name}}<br>
                                    {{$warehouse->address1}}<br>
                                    @if(!empty($warehouse->address2)) 
                                    {{$warehouse->address2}}<br>
                                    @endif
                                    {{$warehouse->city}}, {{$warehouse->state_name}}<br> {{$warehouse->country_name}}, {{$warehouse->pincode}}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12">        
                        <div class="portlet ">            
                            <h4>Supplier</h4>
                            <div class="box2">
                                <div class="row">
                                    <div class="col-md-8">

                                        
                                            @if(empty($selectedSupplier))
                                                <select id="supplier" name="supplier" class="form-control">
                                                <option value="">All Supplier</option>                               
                                                @foreach($suppliers as $supplierData)
                                                    <option value="{{$supplierData['suppliers']}}">{{$supplierData['supplier_name']}}</option>
                                                @endforeach
                                            @else
                                                <select id="supplier" name="supplier" class="form-control" disabled="disabled">
                                                @foreach($suppliers as $supplierData)
                                                    @if($selectedSupplier==$supplierData['suppliers'])
                                                        <option value="{{$supplierData['suppliers']}}" selected="selected">{{$supplierData['supplier_name']}}</option>
                                                    @else
                                                        <option value="{{$supplierData['suppliers']}}">{{$supplierData['supplier_name']}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>

                                        <div id="supplier_addess">
                                        @foreach($suppliers as $supplierData)
                                                   <p>@if($selectedSupplier==$supplierData['suppliers'])
                                                            {{$supplierData['address1']}}<br>
                                                            @if(!empty($supplierDat['address2'])) 
                                                            {{$supplierData['address2']}}<br>
                                                            @endif
                                                            {{$supplierData['city']}}, {{$supplierData['state_name']}}<br>
                                                            {{$supplierData['country_name']}}, {{$supplierData['pincode']}}
                                                            @endif

                                                        </p>
                                                @endforeach
                                        </div>
                                        <div class="pull-right" id="loading" style="display:none;"><img src="/img/ajax-loader.gif"></div>
                                    </div>
                                    @if($updateSupplier_IndentPage == 1)
                                    <div class="col-md-2">
                                        <button id="edit-supplier" class="fa fa-pencil-square-o btn green-meadow" title = "Edit Supplier"></button>
                                    </div>

                                    <div class="col-md-2">
                                        <button id="save-supplier" class="fa fa-floppy-o btn green-meadow" title = "Save Supplier"></button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

                
                <div class="row">
                    <div class="col-md-12">
                        <h4>Product Description</h4>
                        <div>

                            <div class="scroller" style="height: 350px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                <table class="table table-striped table-bordered table-advance table-hover">
                                    <thead>
                                        <tr>
                                            <th align="center" width="5%" class="txtCenter"><strong>S No</strong></th>
                                            <th width="10%" class="txtCenter"><strong>SKU Code</strong></th>
                                            <th width="15%"><strong>Product Name</strong></th>
                                            <!-- <th width="10%" class="txtCenter"><strong>EAN Code</strong></th> -->
                                            <th width="10%" class="txtCenter"><strong>MRP</strong></th>    
                                            <?php //<th width="10%" align="center" class="txtCenter">Avail. Inventory</th> ?>
                                            <th width="10%" align="center" class="txtCenter"><strong>Indent Qty</strong></th>
                                            <th width="10%" align="center" class="txtCenter"><strong>Target CFC {{Lang::get('headings.LP')}}</strong></th>
                                            <th width="10%" align="center" class="txtCenter"><strong>Max CFC {{Lang::get('headings.LP')}}</strong></th>
                                            <?php //<th width="5%" align="center" class="txtCenter">MBQ</th> ?>
                                        </tr>
                                    </thead>
                                    {{ Form::open(['id' => 'update_indents_form']) }}
                                    <input type="hidden" name="indentid" id="indentid" value="{{ $indentArr[0]->indent_id }}">
                                    <tbody>
                                        <?php
                                        $slno = 1;
                                        $sumOfIndentQty = 0;
                                        $sumOfInvQty = 0;
                                        $sumOfMpqQty = 0;
                                        $sumOfOrderedQty = 0;
                                        ?>

                                        @foreach($indentArr as $product)
                                        
                                        <tr class="odd gradeX">
                                            <td align="center">{{$slno}}</td>
                                            <td align="center">{{$product->sku}}</td>
                                            <td>{{$product->pname}}</td>
                                            <!-- <td align="center">
                                                {{(isset($product->upc) ? $product->upc : $product->seller_sku)}}</td> -->
                                            <td align="center">{{number_format($product->mrp, 2)}}</td>
                                            <?php //<td align="center">{{(int)$product->soh}}</td> ?>
                                            <td align="center"><input class="form-control valid input-sm" style="float:left; width:60px;" size='5' type="number" step="1" min='1' name="indentqty_{{$product->sku}}" id="indent_qty_{{$product->sku}}" value="{{(int)$product->qty}}"><span style="float:right; margin-top:5px;">CFC({{$product->no_of_eaches}})</span></td>
                                            <td align="center">{{number_format($product->target_elp,2)}}</td>
                                            <td align="center">{{number_format($product->max_elp,2)}}</td>
                                            <?php //<td align="center">{{(int)$product->mbq}}</td> ?>
                                        </tr>

                                        <?php
                                        $slno = ($slno + 1);
                                        //$sumOfIndentQty = ($sumOfIndentQty + $product->qty);
                                        //$sumOfInvQty = $sumOfInvQty + $product->soh;
                                        //$sumOfMpqQty = $sumOfMpqQty + $product->mbq;
                                        //$sumOfOrderedQty = $sumOfOrderedQty + (isset($product->order_qty) ? (int)$product->order_qty : 0); 
                                        ?>
                                        @endforeach
                                        
                                        <?php /*
                                          <tr>
                                          <td colspan="6">
                                          <div class="pull-right" id="loading" style="display:none;"><img src="/img/ajax-loader.gif"></div>
                                          </td>
                                          <td colspan="2" align="center">
                                          <input type="submit" id="saveIndent" name="Save" value="Save" class="btn green-meadow" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing Order">&nbsp;&nbsp;
                                          <input type="submit" id="approveIndent" name="Approve" value="Approve" class="btn green-meadow">
                                          </td>
                                          </tr>
                                         */ ?>
                                    </tbody>
                                    
                                </table>
                                <input type="button" class="btn green-meadow pull-right" name="indent_update" id="indent_update" value="Update">
                                {{ Form::close() }}
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
<style>
    .notific{font-size: 11px; color:f00;}

    .box2 {
        height: 130px !important;
    }
    .txtCenter {text-align: center !important;}
    .form-control{text-align: center !important;}
</style>
<script type="text/javascript">

    function ajaxSubmit() {
        var form = $('#frm_indent');
        $('#loading').show();
        $.ajax({
            url: form[0].action,
            type: "POST",
            data: form.serialize(),
            dataType: 'json',
            success: function (data) {
                $('#loading').hide();
                $('#ajaxResponse').show();
                $('#ajaxMsg').html(data.message);
                //window.setTimeout(function(){location.reload()},2000);
                window.setTimeout(function () {
                    window.location.href = "/indents"
                }, 2000);
            },
            error: function (response) {

            }
        });
    }

    $(document).ready(function () {

        $('#save-supplier').click(function(){
            var supplierId = $('#supplier').val();
            if(supplierId==''){
                alert("Please select Supplier !");
            } else{
                $('#loading').show();
                $.ajax({
                    url: "/indents/updateIndentSupplier?_token="+$('#csrf-token').val(),
                    type: "POST",
                    data: {"supplierId":supplierId, "indentId":"<?php echo $indentId;?>"},
                    // dataType: 'json',
                    success: function (data) {
                        $('html, body').animate({ scrollTop: 0 }, 0);  //to scroll top
                        $('#loading').hide();
                        if(data == 1 || data == '1')
                        {
                            $("#ajaxResponse").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Supplier Updated !</div></div>');
                        }else{
                            $("#ajaxResponse").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Supplier Updation Failed</div></div>');
                        }
                        $(".alert-success").fadeOut(20000);
                        $('#supplier').attr('disabled', 'disabled');
                        //window.setTimeout(function(){location.reload()},2000);
                        // window.setTimeout(function () {
                        //     window.location.href = "/indents"
                        // }, 1000);
                    },
                    error: function (response) {
                        console.log("failed");
                        $('#loading').hide();
                        $('#ajaxResponse').show();
                        $('#ajaxMsg').html("Error! Please try again..");
                    }
                });
            }
        });

        $('#edit-supplier').click(function(){
            var isDisabled = $('#supplier').is(':disabled');
            if(isDisabled){
                $('#supplier').removeAttr("disabled");
            }
            
        });


        $('#approveIndent').click(function () {
            $('#approveAction').val('approve');

            $("#frm_indent").validate({
                rules: {
                },
                submitHandler: function (form) {
                    ajaxSubmit();
                }
            });

        });

        $('#saveIndent').click(function () {
            $('#approveAction').val('save');
            $("#frm_indent").validate({
                rules: {
                },
                submitHandler: function (form) {
                    ajaxSubmit();
                }
            });
        });
    });

$("#indent_update").click(function(){

    $.ajax({
        url: "/indents/savedetails?_token="+$('#csrf-token').val(),
        type: "POST",
        data: $("#update_indents_form").serialize(),
        dataType: 'json',
        success: function(data)
        {
            // window.setTimeout(function(){location.reload()},2000);
            if(data.status_code == 1)
            {
                window.setTimeout(function () {
                window.location.href = "/indents/detail/<?php echo $indentId ?>";
            }, 100);
            }else if(data.status_code == 2){
                $("body").scrollTop();
                $("#ajaxResponse").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Indent Qty should not be grater than PO Qty for '+data.sku+'</div></div>');
                $(".alert-danger").fadeOut(10000);
            } else{
                $("#ajaxResponse").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Something went wrong, Please try again later !!</div></div>');
                $(".alert-danger").fadeOut(10000);
            }
            
        },
        error: function(data)
        {
            console.log("error");
        }
    });
});

$(".valid").on('blur keyup', function(){
    $.each($('.valid'), function() {
        var indent_qty =  $(this).val();
        if(indent_qty <= 0)
        {
            var id = $(this).attr('id');
            $("#"+id).focus();
            $('html, body').animate({ scrollTop: 0 }, 0);  //to scroll top
            $("#ajaxResponse").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Invalid Indent Quantity</div></div>');
            $(".alert-danger").fadeOut(10000);
            return false;
        }
    });

});

$("#supplier").change(function(){
    var supllie_value = this.value;
    var indentvalue  = "<?php echo $indentId ?>";
    var warehouse_id = "<?php echo $warehouse->le_wh_id ?>";

    $.ajax({
        url: "/indents/getselectedsupplieraddress/"+supllie_value+"?_token="+$('#csrf-token').val(),
        type: "POST",
        data: {"supplierId":supllie_value},
        // dataType: 'json',
        success: function (data) {
            $("#supplier_addess").html("");
            $("#supplier_addess").html("<p><br>"+data.address1+"<br>"+data.city+","+data.state_name+",<br>"+data.country_name +","+data.pincode  +"</p>");
        },
        error: function (response) {
        }
    });

});

</script>
@stop
