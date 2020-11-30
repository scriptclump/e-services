@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="_method" value="POST">
<div class="row">
	<div class="col-md-12">
		<ul class="page-breadcrumb breadcrumb">
			<li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/salesorders/index">Sales Orders</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li>Bulk Shipment</li>
		</ul>
	</div>
</div>
<form id="picklist_form" action="/salesorders/savebulkshipment" method="POST">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="_method" value="POST">
<div class="row">
    <div class="col-md-12">		
        <div class="portlet light portlet-fit portlet-datatable">
			<div class="portlet-title">
				<div class="caption uppercase">Bulk Shipment</div>
				<div class="tools">&nbsp;</div>
			 </div>
			
          
			 <div class="row">
			 	<div class="col-md-12">
			 		<h4>Product Description</h4>
			 		<table id="bulkshipment" class="table table-striped table-bordered table-advance table-hover thline table-scrolling">
                    <thead>
                        <tr>
                            <th width="10%">SKU</th>
                            <th width="25%">Product Name</th>
                            <th width="5%">MRP</th>
                            <th width="10%">Unit Price</th>
                            <th width="10%">Pick Qty</th>                            
                            <th width="10%">Shipment Qty</th>
                            <th width="5%">Cancel</th>
                            <th width="5%">Pending Qty</th>
                            <th width="10%">Status</th>
                            <th width="10%">Cancel Reason</th>
                        </tr>
                    </thead>
                    <?php $statusCodeArr = array();?><?php $orderstr='';?>
                    @if(is_array($ordersArr) && count($ordersArr) > 0)
                        @foreach($ordersArr as $order_code=>$orders)
                        <?php $orderstr=$orderIdsArr[$order_code].','.$orderstr;?>
                        <tr>
                            <td bgcolor="#eee" colspan="5">
                            <?php $orderId = $orderIdsArr[$order_code]; ?>
                            <strong>Order ID:</strong> {{$order_code}}, <strong>Shop Name:</strong> {{$shopNameArr[$order_code]}}</td>

                            <td bgcolor="#eee" colspan="2">
                            <input type="checkbox" name="change_inv_qty[{{$orderId}}]" id="change_inv_qty_{{$orderId}}" value="0" onclick="changeInvQty({{$orderId}});">&nbsp;Change Invoice Qty</td>
                            <td bgcolor="#eee"><strong>CFC</strong>
                                <input style="width:100px; float:left;margin-right:5px;" type="number" name="cartons[{{$orderId}}]" id="cartons" class="form-control" min="0" placeholder="Cartons" value="{{!empty($tracksArr[$orderId]->cfc_cnt) ? $tracksArr[$orderId]->cfc_cnt : '0'}}">
                            </td>
                            <td bgcolor="#eee"><strong>Bags</strong>
                                <input style="width:100px; float:left;margin-right:5px;" type="number" name="bags[{{$orderId}}]" id="bags" class="form-control" min="0" placeholder="Bags" value="{{!empty($tracksArr[$orderId]->bags_cnt) ? $tracksArr[$orderId]->bags_cnt : '0'}}">
                            </td>
                            <td bgcolor="#eee"><strong>Crates</strong>
                                <input style="width:100px; float:left;" type="number" name="crates[{{$orderId}}]" id="crates" class="form-control" min="0" placeholder="Crates" value="{{!empty($tracksArr[$orderId]->crates_cnt) ? $tracksArr[$orderId]->crates_cnt : '0'}}"></td>
                        </tr>
                        @foreach($orders as $order)
                        <?php
                        $pendingShipQty = 0;
                        $unitPrice = $order->total / $order->qty;
                        $orderQty = (int)$order->qty; 
                        $shipQty = (int)($shippedArr[$order->gds_order_id][$order->product_id]);
                        $canceledQty = (int)$canceledArr[$order->gds_order_id][$order->product_id];
                        if(!$shipQty) {
                            $shipmentQty = ($order->qty - $canceledQty);
                        }
                        else {
                            $shipmentQty = ($shipQty > 0 ? (int)$shipQty : (int)$order->qty);
                        }
                        
                        if($order->qty == $canceledQty) {
                            $shipmentQty = 0;
                        }

                        $pendingQty = ($orderQty - ($canceledQty));
                        //$pendingShipQty = ($orderQty - ($canceledQty + $shipQty));
                        $disabledCancel = false;
                        $disabledShip = false;
                        /*if($order->order_status == '17015') {
							$disabledCancel = 'disabled="disabled"';
							$disabledShip = 'disabled="disabled"';
						}
						else if($order->order_status == '17005' || $order->order_status == '17006') {
							$disabledCancel = 'disabled="disabled"';
							$disabledShip = 'disabled="disabled"';
						}
                        else if($order->order_status == '17021') {
                            $disabledCancel = 'disabled="disabled"';
                            $disabledShip = 'disabled="disabled"';
                        }*/

                        $disabledCancel = 'readonly="readonly"';
                        $disabledShip = 'readonly="readonly"';

                        $canReasonCode = isset($cancelReasonArr[$order->gds_order_id][$order->product_id]) ? $cancelReasonArr[$order->gds_order_id][$order->product_id] : '';

                        ?>
                       
                        <tr>
                            <td>{{$order->sku}}</td>
                            <td>{{$order->pname}}</td>
                            <td>{{number_format($order->mrp, 2)}}</td>
                            <td>{{number_format($unitPrice,2)}}</td>
                            <td>{{$order->qty}}
                                <span style="font-size:11px; font-weight:bold;">
                                @if($shipQty)
                                <br>{{$order->order_status_id == '17021' ? 'Invoiced' : 'RTD'}}: {{$shipQty}}
                              
                                @endif
                                @if($canceledQty)
                                 <br> Cancelled: {{$canceledQty}}
                                @endif 
                                </span>
                                <input type="hidden" class="form-control" name="pickQty" id="pickQty-{{$order->gds_order_id.'-'.$order->product_id}}" value="{{($orderQty)}}">
                            </td>
                            
                            <td>
                            <input type="hidden" class="form-control" name="orders[{{$order->gds_order_id}}]" value="{{$order_code}}">

                            <?php /*<input type="hidden" class="form-control" name="prices[{{$order->gds_order_id}}][{{$order->product_id}}]" value="{{$order->price}}">
                            */?>
                            <input type="hidden" class="form-control" name="item_sku[{{$order->product_id}}][]" value="{{$order->sku}}">
							<?php /*
                            @if($order->order_status == '17005')
                            <input type="number" {{$disabledShip}} size="3" min="0" max="{{$shipmentQty}}" class="form-control" name="notuse" id="" value="{{$shipmentQty}}">
                            <input type="hidden" size="4" min="0" max="{{$shipmentQty}}" class="form-control shipmentItems" name="products[{{$order->gds_order_id}}][{{$order->product_id}}]" id="ship-{{$order->gds_order_id.'-'.$order->product_id}}" value="{{$shipmentQty}}">
                            @else                            
                            <input type="number" {{$disabledShip}} size="4" min="0" max="{{$shipmentQty}}" class="form-control shipmentItems" name="products[{{$order->gds_order_id}}][{{$order->product_id}}]" id="ship-{{$order->gds_order_id.'-'.$order->product_id}}" value="{{$shipmentQty}}">
                            @endif
                            */?>
				<input type="number" {{$disabledShip}} size="4" min="0" max="{{$order->qty}}" class="form-control shipmentItems prd_nouse_{{$order->gds_order_id}}" name="products[{{$order->gds_order_id}}][{{$order->product_id}}]" id="ship-{{$order->gds_order_id.'-'.$order->product_id}}" value="{{$shipmentQty}}" prev_val="{{$shipmentQty}}">

                            <input type="hidden" size="3" class="form-control" name="orderedQty[{{$order->gds_order_id}}][{{$order->product_id}}]" value="{{$order->qty}}">

                            </td>
                            <td>
							
                            <input type="number" {{$disabledCancel}} style="width:100px;" size="3" max="{{$order->qty}}" min="0" class="form-control cancelItem prd_nouse_{{$order->gds_order_id}}" name="cancel[{{$order->gds_order_id}}][{{$order->product_id}}]" id="cancel-{{$order->gds_order_id.'-'.$order->product_id}}" value="{{$canceledQty}}" prev_val="{{$canceledQty}}" >
							</td>
                            <td>
                                <input type="text" style="width:100px;" size="3" class="form-control" name="pending-qty" id="pending-{{$order->gds_order_id.'-'.$order->product_id}}" value="{{$pendingShipQty}}" disabled="disabled">
                            </td>
                            <td>{{isset($orderStatus[$order->order_status])?$orderStatus[$order->order_status]:''}}</td>
                            <td>
                            <?php /*<input type="text" class="form-control" name="comment[{{$order->gds_order_id}}][{{$order->product_id}}]" value="">*/?>
                            <select readonly class="form-control prd_can_res_{{$order->gds_order_id}}" name="cancelReason[{{$order->gds_order_id}}][{{$order->product_id}}]">
                                <option value="">Select</option>
                                @foreach ($canReasonArr as $key => $value)
                                <option value="{{$key}}" {{($canReasonCode==$key ? 'selected' : '')}}>{{$value}}</option>
                                @endforeach
                            </select>

                            <?php /*<input type="hidden" class="form-control" name="cancelReason[{{$order->gds_order_id}}][{{$order->product_id}}]" value="{{$canReasonCode}}">
                            */?>
                            </td>
                        </tr>
                        <?php $statusCodeArr[] = $order->order_status_id; ?>
                        @endforeach
                        @endforeach
                    @else
                    <tr>
                        <td colspan="8">No Record Found</td>
                    </tr>
                    @endif    
                    </table>
                    <input type="hidden" id="hidden_orderids_str" value="<?php echo trim($orderstr,',');?>">
			 	</div>
			 </div>
<?php 
$statusArr = array_unique($statusCodeArr);
//print_r($statusArr); echo count($statusArr);
$statusCode = isset($statusArr[0]) ? $statusArr[0] : '';
?>
<input type="hidden" name="statuscode" id="statuscode" value="{{$statusCode}}">
@if($statusCode == '17020' || $statusCode == '17005' || $statusCode == '17013')
			 <div class="row">
                <div class="col-md-12">                 
                    <h4>Tracking Information</h4>
                    <table class="table table-striped table-bordered table-advance table-hover thline table-scrolling" id="track_data">
                        <thead>
                            <tr>
                                <th>Carrier<span class="error">*</span></th>
                                <th>Service Name<span class="error">*</span></th>
                                <th>Tracking Number</th>
                                <th>Vehicle Number</th>
                                <th>Representative Name</th>
                                <th>Contact Number</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        <tr>
                                <td width="20%">                                    
                                    <select id="courier" name="courier" class="form-control">
                                       <option value="">Select</option>    
                                        @foreach ($couriers as $key => $value)
                                          <option value="{{$value->carrier_id}}">{{$value->carrier}}</option> 
                                        @endforeach
                                    </select>
                                </td>
                                <td width="20%" id="service_selectbox">
                                    <select id="service_name" name="service_name" class="form-control">
                                        <option value="">Select Service</option>  
                                    </select>
                                </td>
                                <td width="15%"><input type="text" id="track_number" name="track_number" class="form-control"></td>
                                <td width="15%"><input type="text" id="vehicle_number" name="vehicle_number" class="form-control"></td>
                                <td width="15%">
                                    <select class="form-control select2me" name="representative_name" id="representative_name">
                                    <option value="">Please select</option>    
                                    @foreach($deliveryUsers as $User)
                                    <option value="{{ $User->user_id }}" mobile-no="{{ $User->mobile_no }}">{{ $User->firstname.' '.$User->lastname }}</option>
                                    @endforeach
                                    </select>

                                <td width="15%"><input type="number" pattern="\d{10}" id="contact_num" name="contact_num" class="form-control" maxlength="10"></td>
                                
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
            </div>
@endif            
@if(count($ordersArr))
			              
               
                   
                           <div style="display:none;" id="ajaxResponse" class="alert alert-success">
                              
                            </div>

                    	<div class="row">
                            <div class="col-md-6">
                                <h4>Comment</h4>
                                <textarea name="shipment_comment" id="shipment_comment" class="form-control" rows="4"></textarea>
                            </div>
                            <div class="col-md-6 topmargbut">
                            <a href="/salesorders/index" class="btn green-meadow">Back</a>    
                            </div>
                        </div>

                        <br>
	        
           
            <div class="row">
                            <div class="col-md-12">
                                <div class="">
                                    @if(count($statusArr) == 1 && ($statusCode == '17020'))
                                    <input type="submit" disabled="" name="btnPicked" id="btnPickedSubmit" class="btn green-meadow" value="Ready to Dispatch">
                                    @endif

                                    @if(count($statusArr) == 1 && ($statusCode == '17020' || $statusCode == '17005' || $statusCode == '17013'))
                                    <input type="submit" name="btnInvoice" id="btnInvoiceSubmit" class="btn green-meadow" value="Invoice">
                                    @endif
                                   
                                </div>
                            </div>
                        </div>
       
	
@endif

        </div>
	</div>
</div>
</form>
@stop

@section('style')
<style type="text/css">
   /* .fa-remove{ color: #fff !important; }*/
   .topmargbut{ margin-top: 60px !important; }
</style>
@stop


@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/admin/pages/scripts/orders/orders.js') }}" type="text/javascript"></script>
<script type="text/javascript">

function changeInvQty(orderId) {
   
    var isChecked = $('#change_inv_qty_'+orderId).is(':checked');
    
    if(isChecked) {
        if(confirm('Do you want to change invoice quantity?')) {
            $('.prd_nouse_'+orderId).removeAttr('readonly');
            $('#change_inv_qty_'+orderId).attr('checked', true);
            //$('.prd_can_res_'+orderId).prop('disabled',false);
            $('.prd_can_res_'+orderId).removeAttr('readonly');

            $('#change_inv_qty_'+orderId).val(orderId);
        }
        else {
            $('#change_inv_qty_'+orderId).removeAttr('checked');
        }      
        
        
    }

    if(isChecked == false) {

        if(confirm('Do you want to fill previous invoice quantity ?')) {
            $('.prd_nouse_'+orderId).attr('readonly', true);
            //$('.prd_can_res_'+orderId).prop('disabled', true);
            $('.prd_can_res_'+orderId).attr('readonly',true);
            $('#change_inv_qty_'+orderId).val(0);
            $('.prd_nouse_'+orderId).each(function() {
                $(this).val($(this).attr('prev_val'));
            });
        }
        else {
            $('#change_inv_qty_'+orderId).prop('checked', true);
            $('#change_inv_qty_'+orderId).val(orderId);
        }       

    }
}

function submitForm() {
    $('.loderholder').show();
    var form = $('#picklist_form');
       $.ajax({
                url: form[0].action,
                type: "POST",
                data: form.serialize(),
                dataType: 'json',
                success: function(data) {

                    $('.loderholder').hide();
                    if(data.status == 200) {
                        $('.loderholder').show();
                        $('#ajaxResponse').html('Successfully created!').show();
                    setTimeout("window.location.href='/salesorders/index'", 2000);    
                    }
                    else{
                        $('.loderholder').hide();
                        $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html(data.message).show();
                    }                 
                   
                },
                error:function(response){
                    $('.loderholder').hide();
                    $('#ajaxResponse').html('Unable to saved comment');
                }
        });
 }
    $(document).ready(function() {
        $("#bulkshipment").on('click', '.btnDelete', function () {
            if(confirm('Do you want to remove this product?')) {
                $(this).closest('tr').remove();
            }            
        });

        $(".shipmentItems").on('change', function(){
            var id = $(this).attr('id');
            var breakID = id.split("-");

            var newID = '-'+breakID[1]+'-'+breakID[2];

            if(Number($('#pickQty'+newID).val()) < Number($('#ship'+newID).val()) || Number($('#ship'+newID).val()) < 0 || $('#ship'+newID).val() == 'NaN' || $('#ship'+newID).val() == ''){
                alert('Invalid Shipment Qty.');

                $('#ship'+newID).val(0);
                var pending = Number($('#pickQty'+newID).val())-(Number($('#ship'+newID).val())+Number($('#cancel'+newID).val()));
                if(Number(pending)>0){
                    $('#pending'+newID).val(pending);
                }
            }
            else{
                var checkInt = isInt(Number($('#ship'+newID).val()));
                //alert(checkInt);
                if(checkInt === false){
                    alert('Invalid Shipment Qty.');
                    $('#ship'+newID).val(0);
                }

                var cancle = Number($('#pickQty'+newID).val())-Number($('#ship'+newID).val());
                $('#cancel'+newID).val(cancle);
                var pending = Number($('#pickQty'+newID).val())-(Number($('#ship'+newID).val())+Number($('#cancel'+newID).val()));
                $('#pending'+newID).val(pending);
            }            
        });
        $(".cancelItem").on('change', function(){
            var id = $(this).attr('id');
            var breakID = id.split("-");

            var newID = '-'+breakID[1]+'-'+breakID[2];

            if(Number($('#pickQty'+newID).val()) < Number($('#cancel'+newID).val()) || Number($('#cancel'+newID).val()) < 0 || $('#cancel'+newID).val() == 'NaN' || $('#cancel'+newID).val() == ''){
                alert('Invalid Cancel Qty.');

                $('#cancel'+newID).val(0);
                var pending = Number($('#pickQty'+newID).val())-(Number($('#ship'+newID).val())+Number($('#cancel'+newID).val()));
                if(Number(pending)<0){
                    alert('Invalid Cancel Qty.');
                    $('#cancel'+newID).val(0);
                }
                else{
                    $('#pending'+newID).val(pending);
                }
            }
            else{
                var checkInt = isInt(Number($('#cancel'+newID).val()));
                if(checkInt === false){
                    alert('Invalid Cancel Qty.');
                    $('#cancel'+newID).val(0);
                }
                var pending = Number($('#pickQty'+newID).val())-(Number($('#ship'+newID).val())+Number($('#cancel'+newID).val()));
                if(Number(pending)<0){
                    alert('Invalid Cancel Qty.');
                    $('#cancel'+newID).val(0);
                }
                else{
                    $('#pending'+newID).val(pending);
                }
                
            }            
        });

        $('#courier').change(function () {
            getCarrierServices();
        });

        $('#btnPickedSubmit').click(function () {
            var res = confirm("Selected orders will move to 'Ready to Dispatch'.");
            if(res==true){
                $('#courier').attr('disabled', true);
                $('#service_name').attr('disabled', true);
                $('#track_number').attr('disabled', true);
                $('#vehicle_number').attr('disabled', true);
                $('#representative_name').attr('disabled', true);
                $('#contact_num').attr('disabled', true);
                
                $("#picklist_form").validate({
                       rules: {                
                                
                            },
                            submitHandler: function(form) {
                                submitForm();
                            } 
                });
            }
            else{
                $("#picklist_form").submit(function(e){
                    e.preventDefault();
                });
            }           
        });

        $('#btnDeliveredSubmit').click(function () {
            $("#picklist_form").validate({
                   rules: {                
                            
                        },
                        submitHandler: function(form) {
                            submitForm();
                        } 
            });
        });

        $('#btnInvoiceSubmit').click(function () {
            var statuscode=$('#statuscode').val();
            var hidden_orderids_str=$('#hidden_orderids_str').val();
            hidden_orderids_str=hidden_orderids_str.split(',');
            if(statuscode==17020){
                for(var i=0;i<hidden_orderids_str.length;i++){
                    console.log(hidden_orderids_str[i]);
                    $('#change_inv_qty_'+hidden_orderids_str[i]).prop('checked', true);
                    $('#change_inv_qty_'+hidden_orderids_str[i]).val(hidden_orderids_str[i]);
                    //changeInvQty(hidden_orderids_str[i]);
                }
            }
            var res = confirm("Selected orders will move to 'Invoice'.");
            if(res==true){
                $('#courier').attr('disabled', false);
                $('#service_name').attr('disabled', false);
                $('#track_number').attr('disabled', false);
                $('#vehicle_number').attr('disabled', false);
                $('#representative_name').attr('disabled', false);
                $('#contact_num').attr('disabled', false);


                $("#picklist_form").validate({
                       rules: {                
                                courier:"required",
                                service_name:"required"
                            },
                            submitHandler: function(form) {
                                submitForm();
                            } 
                });

                var cancels = $('select[name^="cancelReason"]');
                cancels.each(function() {

                    if($(this).parents('tr').find('.cancelItem').val()!='0') {
                      $(this).rules('add',"required");  
                  } else {
                      $(this).rules('remove',"required");
                      $(this).removeClass('error');  
                  }

                });

            }
            else{
                $("#picklist_form").submit(function(e){
                    e.preventDefault();
                });
            } 
        });      

        $('#representative_name').on('change',function(){
            $('#contact_num').val($(this).find('option:selected').attr('mobile-no'))
        })

        function isInt(n) {
           return n % 1 === 0;
        }
    }); 
</script>
<style type="text/css"> .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
.loderholder img{ position: absolute; top:50%;left:50%;    }
.error{color: red;}
</style>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop
