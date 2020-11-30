<?php
$style = "";
    if($indentArr[0]->legal_entity_id == "") {
        $style = 'style=display:none';
    }
    // echo $style;die;
?>

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
                
                <div class="actions"> 
               
                 @if($createPO_IndentPage == 1 && $indent_Status == 70001)                   
                    <a id="createPO" {{ $style }} href="/po/create?indentid={{$encode_indent_id}}" class="btn green-meadow">Create PO</a>
                @endif
                
                    <!-- <a href="/indents/print/{{$indentArr[0]->indent_id}}" class="btn green-meadow"><i class="fa fa-print"></i></a>
                    <a href="/indents/pdf/{{$indentArr[0]->indent_id}}" class="btn green-meadow"><i class="fa fa-download"></i></a>
                    <a href="/indents/autoindent" class="btn green-meadow">Auto Indent</a> -->
                @if($EditIndent_indentPage == 1 && $indent_Status == 70001) 

                    <a href="/indents/editdetails/{{$indentArr[0]->indent_id}}" class="btn green-meadow">Edit Indent</a>
                @endif
                @if($indentArr[0]->business_legal_name!='')
                <a href="/indents/print/{{$indentArr[0]->indent_id}}" target="_blank"><i class="fa fa-print btn green-meadow"></i></a>&nbsp;<a href="/indents/pdf/{{$indentArr[0]->indent_id}}"><i class="fa fa-download btn green-meadow"></i></a>
                @endif
                </div>

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
            
            @if(Session::get('message') == 'success')
                <span id="ajaxResponse_1"><div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Supplier quantity Updated !</div></div></span>
            @else
                <span id="ajaxResponse"></span> 
            @endif 
            {{Session::forget('message')}}           
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
                                        <select id="supplier" name="supplier" class="form-control select2me">
                                                <option value="">All Supplier</option>                               
                                                @foreach($suppliers as $supplierData)
                                                    <option value="{{$supplierData['legal_entity_id']}}">{{$supplierData['business_legal_name']}}</option>
                                                @endforeach
                                        </select>
                                            @else
                                        <select id="supplier" name="supplier" class="form-control select2me" disabled="disabled">
                                                @foreach($suppliers as $supplierData)
                                                    @if($selectedSupplier==$supplierData['legal_entity_id'])
                                                        <option value="{{$supplierData['legal_entity_id']}}" selected="selected">{{$supplierData['business_legal_name']}}</option>
                                                    @else
                                                        <option value="{{$supplierData['legal_entity_id']}}">{{$supplierData['business_legal_name']}}</option>
                                                    @endif
                                                @endforeach
                                        </select>
                                        @endif
                                        <div id="supplier_addess">
                                            <p>@if(isset($suppliers[$selectedSupplier]))
                                                <?php $supplierData = $suppliers[$selectedSupplier]; ?>
                                                            {{$supplierData['address1']}}<br>
                                                            @if(!empty($supplierDat['address2'])) 
                                                            {{$supplierData['address2']}}<br>
                                                            @endif
                                                            {{$supplierData['city']}}, {{$supplierData['state_name']}}<br>
                                                            {{$supplierData['country_name']}}, {{$supplierData['pincode']}}
                                                            @endif
                                                        </p>
                                        </div>
                                        <div class="pull-right" id="loading" style="display:none;"><img src="/img/ajax-loader.gif"></div>
                                    </div>
                                    @if($updateSupplier_IndentPage == 1 && $indent_Status == 70001)
                                    <div class="col-md-2">
                                        <button id="edit-supplier" class="fa fa-pencil-square-o btn green-meadow" title="Edit Supplier"></button>
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
                            <div data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                <table class="table table-striped table-bordered table-advance table-hover">
                                    <thead>
                                        <tr>
                                            <th align="center" width="5%" class="txtCenter"><strong>S No</strong></th>
                                            <th width="10%" class="txtCenter"><strong>SKU Code</strong></th>
                                            <th width="15%"><strong>Product Name</strong></th>
                                            <!-- <th width="10%" class="txtCenter">EAN Code</th> -->
                                            <th width="10%" class="txtCenter"><strong>MRP</strong></th>    
                                            <?php //<th width="10%" align="center" class="txtCenter">Avail. Inventory</th> ?>
                                            <th width="10%" align="center" class="txtCenter"><strong>Inventory Qty</strong></th>
                                            <th width="10%" align="center" class="txtCenter"><strong>Indent Qty</strong></th>
                                            <!-- <th width="10%" align="center" class="txtCenter"><strong>Target CFC {{Lang::get('headings.LP')}}</strong></th> -->
                                            <th width="10%" align="center" class="txtCenter"><strong>CFC {{Lang::get('headings.LP')}}</strong></th>
                                            <?php //<th width="5%" align="center" class="txtCenter">MBQ</th> ?>
                                        </tr>
                                    </thead>
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
                                          <!--   <td align="center">
                                                {{(isset($product->upc) ? $product->upc : $product->seller_sku)}}</td> -->
                                            <td align="center">{{number_format($product->mrp, 2)}}</td>
                                            <td>{{$product->available_inv}}</td>
                                            <?php //<td align="center">{{(int)$product->soh}}</td> ?>
                                            <td align="center">{{(int)$product->qty}}<span style="margin-left: 5px; margin-top:5px;">{{ $product->packtype }}({{$product->prod_eaches}})</span></td>
                                            <td align="center">{{number_format($product->target_elp,2)}}</td>
                                            <!--<td align="center">{{number_format($product->max_elp,2)}}</td>-->
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
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<style>
    .notific{font-size: 11px; color:f00;}

    .box2 {
        height: 160px !important;
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
                            $("#createPO").show();
                            $("#ajaxResponse").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Supplier Updated </div></div>');
                        }else if(data == "indent-closed"){
                            $("#ajaxResponse").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>This Indent already closed !!</div></div>');
                        } else if(data == 0 || data == '0'){
                            $("#ajaxResponse").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>This supplier is already selected !!</div></div>');
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
