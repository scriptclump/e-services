@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/salesorders/index">Sales Orders</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Order Details</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption uppercase">Order #{{$orderdata->order_code}}&nbsp;&nbsp;{{date('d-m-Y H:i:s',strtotime($orderdata->order_date))}}
                </div>
                <div class="tools uppercase">&nbsp;</div>
                <button class="btn green-meadow pull-right" href="#reorder" data-toggle="modal" onclick="refreshTable()">Add/Edit product</button>

             </div>
            
            <div class="portlet-body">
                <div class="orderdet">
                    <table class="table table-bordered thline table-scrolling">
                        <thead>
                            <tr>
                                <th> Order Details </th>
                                <th> Customer Details </th>
                            </tr>
                        </thead>   
                        <tbody>
                            <tr>
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                            <input type="hidden" name="le_wh_id" id="le_wh_id" value="{{$orderdata->le_wh_id}}">
                            <input type="hidden" name="customer_type" id="customer_type" value="{{$retailerInfo->legal_entity_type_id}}">
                            <input type="hidden" name="order_id" id="order_id" value="{{$order_id}}">
                                <td class="col-md-4"><div class="portlet-body  ">
                                        <div class="static-info"> <div class="row">
                                            <div class="col-md-5 name"> Order ID: </div>
                                            <div class="col-md-7 value"> {{!empty($orderdata->order_code) ? $orderdata->order_code : $orderdata->gds_order_id}}</div>
                                        </div></div>                      
                                        <div class="static-info"><div class="row">
                                            <div class="col-md-5 name"> Order Date: </div>
                                            <div class="col-md-7 value"> {{date('d-m-Y H:i:s',strtotime($orderdata->order_date))}} </div>
                                        </div></div>
                                        @if(!empty($orderdata->order_expiry_date))
                                         <div class=" static-info"><div class="row">
                                            <div class="col-md-5 name"> Order Expiry Date: </div>
                                            <div class="col-md-7 value"> {{date('d-m-Y H:i:s',strtotime($orderdata->order_expiry_date))}} </div>
                                        </div></div>
                                        @endif
                                       
                                    </div>
                                  <div class=" static-info"><div class="row">
                                            <div class="col-md-5 name"> Channel:     </div>
                                            <div class="col-md-7 value"> {{$orderdata->mp_name}} </div>
                                        </div></div>                
                                         <div class=" static-info"><div class="row">
                                            <div class="col-md-5 name"> URL:     </div>
                                            <div class="col-md-7 value"> {{$orderdata->mp_url}} </div>
                                        </div></div>   
                                        @if(is_object($whInfo))
                                        <div class=" static-info">
                                            <div class="row">
                                                <div class="col-md-5 name">Warehouse:</div>
                                                <div class="col-md-7 value"> {{$whInfo->lp_wh_name}} </div>
                                            </div>
                                        </div>  
                                        @endif

                                        @if(is_object($hubInfo))
                                        <div class=" static-info">
                                            <div class="row">
                                                <div class="col-md-5 name">Hub Name:</div>
                                                <div class="col-md-7 value"> {{$hubInfo->lp_wh_name}} </div>
                                            </div>
                                        </div>  
                                        @endif

                                        <div class=" static-info"><div class="row">
                                                <div class="col-md-5 name">Spoke: </div>
                                                <div class="col-md-7 value"> {{$orderdata->spokeName}}</div>
                                            </div>
                                        </div>            

                                        <div class=" static-info"><div class="row">
                                                <div class="col-md-5 name">Beat: </div>
                                                <div class="col-md-7 value"> {{$orderdata->beat}}</div>
                                            </div>
                                        </div>            

                                        @if(is_object($userInfo) && isset($userInfo->firstname) && isset($userInfo->lastname))
                                        <div class=" static-info">
                                            <div class="row">
                                                <div class="col-md-5 name">Created By:</div>
                                                <div class="col-md-7 value">{{$userInfo->firstname}} {{$userInfo->lastname}} (M: {{isset($userInfo->mobile_no) ? $userInfo->mobile_no : ''}})</div>
                                            </div>
                                        </div>  
                                        @endif
                                    
                                    
                                    <div class=" static-info"><div class="row">
                                            <div class="col-md-5 name">Self Order: </div>
                                            <div class="col-md-7 value"> {{($orderdata->is_self == 0) ? 'No' : 'Yes'}}</div>
                                        </div>
                                    </div>            
                                    
                                    </div></td>
                                
                                <td class="col-md-4">   <div class="portlet-body  ">
                                        <div class="static-info"><div class="row">
                                            <div class="col-md-5 name"> Retailer Name: </div>
                                            <div class="col-md-7 value"> {{$orderdata->shop_name}} </div>
                                        </div></div>
                                        <div class="static-info"><div class="row">
                                            <div class="col-md-5 name"> Retailer Code: </div>
                                            <div class="col-md-7 value"> {{$orderdata->le_code}} </div>
                                        </div></div>
                                        <div class="static-info"><div class="row">
                                            <div class="col-md-5 name"> Name: </div>
                                            <div class="col-md-7 value"> {{$orderdata->firstname}} {{$orderdata->lastname}}</div>
                                        </div></div>
                                        <div class="static-info"><div class="row">
                                            <div class="col-md-5 name"> Phone: </div>
                                            <div class="col-md-7 value"> {{$orderdata->phone_no}}</div>
                                        </div></div>
                                        <div class="static-info"><div class="row">
                                            <div class="col-md-5 name"> Email:</div>
                                            <div class="col-md-7 value"> {{$orderdata->email}} </div>
                                        </div> </div>

                                        </div>

                                    </div></td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped thline">
                <thead>
                    <tr>
                        <th> SNo </th>
                        <th> SKU# </th>
                        <th> Product Name</th>
                        <th> HSN Code</th>
                        <th> Ordered Qty </th>
                        <th> MRP </th>
                        <th> Unit Base Price</th>
                        @if($orderdata->discount_before_tax==1)
                        <th> Cost </th>                                
                        <th> Discount </th>
                        @endif
                        <th> Net Value </th>
                        <th> Tax %</th>
                        <th> Tax Value</th>
                        @if($orderdata->discount_before_tax==0)
                        <th> Discount </th>
                        @endif
                        <th style="text-align:right;"> Total </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sno = 1;
                        $totalQty = 0;
                        $totalDiscount = 0;
                        $sumOfSubtotal = 0;
                        $sumOfSubTax = $sumOfNetValue = 0;
                        $discount_before_tax_total = 0;
                    ?>

                    @foreach($products as $product)
                        <?php $tax_percent = (isset($productTaxArr[$product->product_id]) ? $productTaxArr[$product->product_id] : 0);
                            $orderQty = $product->qty;
                            $totalQty += $orderQty;
                            $unitBasePrice = ((round($product->total,2)/(100+$tax_percent))*100)/$orderQty;
                            $netValue = $unitBasePrice * $orderQty;
                            $sumOfNetValue+= $netValue;
                            $sumOfSubTax+=$product->tax;
                            $sumOfSubtotal+=$product->total;
                        ?>
                        <tr>
                            <td>{{$sno++}}</td>
                            <td>{{$product->sku}}<input type="hidden" id="p_id" value="{{$product->product_id}}"/></td>
                            <td>{{$product->pname}}</td>
                            <td>{{$product->hsn_code}}</td>
                            <td>{{$product->qty}}</td>
                            <td>{{$orderdata->symbol}} {{number_format($product->mrp, 2)}}</td>
                            <td>{{$orderdata->symbol}} {{round($unitBasePrice, 2)}}</td>
                            @if($orderdata->discount_before_tax==1)
                            <td>{{$orderdata->symbol}} {{number_format($product->cost,2)}}</td>
                            <td>{{$orderdata->symbol}} {{($product->discount_type=='value') ? number_format($product->discount_amt,2) : number_format($product->discount_amt,2).'('.$product->discount.'%)'}}</td>
                            @endif
                            <td>{{round($netValue,2)}}</td>
                            <td>
                                {{(isset($productTaxArr[$product->product_id]) ? (float)$productTaxArr[$product->product_id].'%' : '0.0%')}}
                                </td>
                            <td>{{$orderdata->symbol}} {{round($product->tax, 2)}}</td>
                            @if($orderdata->discount_before_tax==0)
                                <td>{{$orderdata->symbol}} {{($product->discount_type=='value') ? number_format($product->discount_amt,2) : number_format($product->discount_amt,2).'('.$product->discount.'%)'}}</td>
                            @endif
                            <td align="right">{{$orderdata->symbol}} {{round($product->total, 2)}}</td>
                            @if($unitBasePrice>0)
                            <td  onclick="deleteItem({{$product->product_id}},{{$product->qty}},{{$product->tax}},{{$product->total}});"><i class="fa fa-trash" aria-hidden="true" style="font-size:22px" ></i></td>
                            @endif

                        </tr>
                    
                    @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{round($sumOfNetValue,2)}}</td>
                            <td></td>
                            <td>{{round($sumOfSubTax,2)}}</td>
                            <td></td>
                            <td>{{round($sumOfSubtotal,2)}}</td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal modal-scroll fade in" id="reorder" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Add/Edit product</h4>
                
            </div>
            <div class="modal-body" style="height: 70%" >
                <input type="text" class="form-control auto-comp" id="selct_product" name="selct_product" />
                <input type = "hidden" id = "mdl_products"  name = "mdl_products" class="form-control"/>
                <input type="hidden" id="product_title" name="product_title" class="form-control">
                <input type="hidden" id="sku" name="sku" class="form-control">
                <input type="hidden" id="mrp" name="mrp" class="form-control">
                <div class="" style="padding-top: 10px;padding-bottom:10px;">Available Inv:<input type="number" min="0" style="" id="available_inv" readonly /></div>
                <div>                
                <table  class="table table-bordered thline table-scrolling" id="packs_list" style="display: none">
                    <thead>
                        <tr>
                            <th>Packs</th>
                            <th>ESU</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                </table>
                <button class="btn green-meadow" id="add_product" name="add_product"  style="display:block;margin: auto;text-align: center;" onclick="addProduct()">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
<style type="text/css">
    .ui-menu-item{
        tabindex:1;
    }
</style>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<script type="text/javascript">
    console.log('$');
    var no_of_packs =0;

$(function() {
    console.log('**');
    $( "#selct_product" ).autocomplete({
        minLength:2,
        source:'/salesorders/getlist?term='+$('#selct_product').val()+"&le_wh_id="+$('#le_wh_id').val()+"&customer_type="+$('#customer_type').val(),
        select: function (event, ui) {
            console.log(ui);
            $('#packs_list tbody').html('');
            $('#available_inv').val(0);
            $('#packs_list').css("display","none");
            no_of_packs = 0;
            var label = ui.item.label;
            var sku = ui.item.sku;
            var product_id = ui.item.product_id;
            var mrp = ui.item.mrp;
            $('#mdl_products').val(product_id);
            $('#product_title').val(label);
            $('#sku').val(sku);
            $('#mrp').val(mrp);
            $('#product_id').val('product_id');

            getProductData();
        }
    });
    function getProductData(){    
        console.log('changed');
        no_of_packs=0;
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url:'/salesorders/getpacks',
            type:'post',
            data:{
                product_id: $('#mdl_products').val(),
                customer_type: $('#customer_type').val(),
                le_wh_id: $('#le_wh_id').val()
            },
            success:function(res){

                console.log(res);
                console.log(res['available_inv']);
                $('#packs_list').append('<tr>')
                let qty =0;
                no_of_packs = res['data'].length;
                $('#available_inv').val(res['available_inv']);
                res['data'].forEach(function(data){
                    console.log(data);
                    qty++;
                    let {level_name,pack_size,unit_price,pack_level,star,esu}=data;
                    let row_id='qty_'+qty;
                    let no_of_units = 'no_of_units_'+qty;
                    let eaches_id = 'eaches_'+qty;
                    let price = 'price_'+qty;
                    let total_price = 'totalprice_'+qty;
                    let level ='level_'+qty;
                    let star_id = 'star_'+qty;
                    let esu_id = 'esu_'+qty;
                    unit_price = +(unit_price);
                    unit_price = unit_price.toFixed(2);
                    if(esu == null || esu == 0)
                        esu=1;
                    var htmldata = `<tr>
                                    <td><input class="form-control" readonly type="text" value='${level_name} - ${pack_size} * ${esu}' style="width:120px"/><input type="hidden" id="${level}" value='${pack_level}' /></td>
                                    <td><input  class="form-control" id="${no_of_units}" type="number"  min="0" onkeyup='setQty(${qty}); productTotal();' onchange='setQty(${qty}); productTotal();' /><input type="hidden" id="${eaches_id}"" value='${pack_size}' /><input type="hidden" id="${star_id}"" value='${star}' /><input type="hidden" id="${esu_id}"" value='${esu}' /></td>
                                    <td><input class="form-control" readonly id="${row_id}" value="0" /></td>
                                    <td><input class="form-control" readonly id="${price}" value='${unit_price}'</td>
                                    <td><input class="form-control" readonly id="${total_price}" </td>
                                    </tr>`;
                    $('#packs_list').append(htmldata);
                });

                $('#packs_list').append(`<tr><td></td><td></td><td></td><td></td><td><input class="form-control" type="number" min="0" readonly id="product_bill" ></td></tr>`);
                $('#packs_list').css("display","block");

            }
        })
    }

   
});
function setQty(index){
    var qty = $(`#eaches_${index}`).val();
    var units = $(`#no_of_units_${index}`).val();
    var price =$(`#price_${index}`).val();
    var esu =$(`#esu_${index}`).val();
    console.log(qty,units,price,esu);
    var finalqty = qty*units*esu;
    $(`#qty_${index}`).val(finalqty);
    var finalprice = finalqty * price;
    $(`#totalprice_${index}`).val(finalprice);

}
function productTotal(){

    console.log('no_of_packs',no_of_packs);
    let bill_value=0;
    for(let key=1;key<=no_of_packs;key++){
        console.log(key);
        console.log($(`#totalprice_${key}`).val());
        bill_value += +($(`#totalprice_${key}`).val());
    }

    $('#product_bill').val(bill_value);
}
function addProduct(){
    var product_id_val = $('#mdl_products').val();
    if(product_id_val){
        if(no_of_packs == 0){
            alert('Please select a pack');
        }else{

            var qty_check = false;
            let total_bill=0;

            let total_qty = 0;
            let json_data  =[];

            for(let index=1;index<=no_of_packs;index++){
                let prod_qty = $(`#qty_${index}`).val();
                if(prod_qty>0){
                    qty_check = true;
                }
                total_bill += +($(`#totalprice_${index}`).val()); 
                total_qty += +(prod_qty);
                if(prod_qty > 0){
                    let data ={
                        pack_level : $(`#level_${index}`).val(),
                        no_of_units: $(`#no_of_units_${index}`).val(),
                        qty: prod_qty,
                        unit_price: $(`#price_${index}`).val(),
                        pack_total: $(`#totalprice_${index}`).val(),
                        star: $(`#star_${index}`).val(),
                        esu: $(`#esu_${index}`).val()
                    };
                    json_data.push(data);  
                }  
                       
            }
            if(qty_check){
                $.ajax({
                    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                    type:'post',
                    url:'/salesorders/addproductintoorder',
                    data: {
                        product_data:json_data,
                        order_id:$('#order_id').val(),
                        le_wh_id:$('#le_wh_id').val(),
                        product_id:$('#mdl_products').val(),
                        product_title: $('#product_title').val(),
                        sku:$('#sku').val(),
                        mrp: $('#mrp').val(),
                        total_qty,
                        customer_type: $('#customer_type').val()
                    },
                    success:function(res){
                        console.log(res);
                        alert(res.message);
                        location.reload();
                    }
                });
            }else{
                alert('Product quantity should be greater than zero');
            }
            //  json_data = JSON.stringify(json_data);

            
        }
    }else{
        alert('Please select a product');
    }
    
}
function deleteItem(p_id,qty,tax,total){
    console.log('pid',p_id,qty,tax,total);
    let productData ={};
    productData.tax = tax;
    productData.qty = qty;
    productData.total = total;
    productData.discount = 0;
    productData.discount_amt = 0;

    console.log(productData);
    $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        type:'post',
        url:'/salesorders/deleteproductintoorder',
        data: {
            order_id:$('#order_id').val(),
            le_wh_id:$('#le_wh_id').val(),
            product_id: p_id,
            product_data: productData,
            customer_type: $('#customer_type').val()

        },
        success:function(res){
            console.log(res);
            alert(res.message);
            location.reload();
        }
    });
}
function refreshTable(){
    $('#packs_list tbody').html('');
    $('#packs_list').css("display","none");
    $('#selct_product').val('');
    $('#sku').val('');
    $('#mdl_products').val('');
    $('#available_inv').val(0);
    no_of_packs = 0;

}

$('#selct_product').change(function(){
    console.log('**change**');
    refreshTable();
})

</script>