@if(count($shipmentArr))			
		<form id="order_shipment_form" action="/salesorders/createShipment" method="POST">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="_method" value="POST">
			<input type="hidden" name="gds_order_id" id="shiment_order_id" value="{{$orderdata->gds_order_id}}">
			<div class="row">			
				<div class="col-md-12">
					<h4>Product Description</h4>
					<table class="table table-striped table-bordered table-advance table-hover thline">
					<thead>
						<tr>
							<th>SKU</th>
							<th width="20%">Product Name</th>
							<th>MRP</th>
							<th>Unit Price</th>
							<th>Ordered Qty</th>
							<th>Shipment Qty</th>
							<th>Comment</th>
						</tr>
					</thead>
					<tbody>
					@if(count($shipmentArr))
					<?php $checkMinQty = (count($shipmentArr) > 1 ? 0 : 1); ?>
						@foreach($shipmentArr as $product)
						<?php $unitPrice =  ($product['total'] / $product['qty']); ?>
						<tr>
							<td>{{$product['sku']}}</td>
							<td>{{$product['pname']}}</td>
							<td>{{$product['mrp']}}</td>
							<td>{{number_format($unitPrice,2)}} </td>
							<td>{{(int)$product['qty']}}<br>
							<span style="font-size:10px;">
							
						@if(isset($product['cancelQty']) && $product['cancelQty'] >0)	Cancelled Qty: {{$product['cancelQty']}}<br> @endif
							
						@if(isset($product['shipedQty']) && $product['shipedQty'] >0)	Shipped Qty: {{$product['shipedQty']}} @endif

							</span></td>
							<td>
								<input type="number"  min="{{$checkMinQty}}" max="{{$product['shipQty']}}" value="{{(int)$product['shipQty']}}" name="available_qty[{{$product['product_id']}}]">
							</td>
							
							<td>
							<textarea rows="1" name="comments[{{$product['product_id']}}]"></textarea><input type="hidden" name="orderItems[{{$product['product_id']}}]" value="{{$product['product_id']}}">
							<input type="hidden" value="{{(int)$product['qty']}}" name="item_qty[{{$product['product_id']}}]">
							<input type="hidden" value="{{$product['sku']}}" name="item_sku[{{$product['product_id']}}]">								
							</td>
						</tr>						
						@endforeach
						@else
						<tr>
							<td colspan="6">No Product available for shipment</td>
						</tr>
						@endif	
					</tbody>
					</table>
				</div>
			</div>
                        
			<div class="row">
				<div class="col-md-12">					
					<h4>Tracking Information</h4>
					<table class="table table-striped table-bordered table-advance table-hover thline" id="track_data">
						<thead>
							<tr>
								<th>Carrier</th>
								<th>Service Name</th>
								<th>Tracking Number</th>
								<th>Vehicle Number</th>
								<th>Representative Name</th>
								<th>Contact Number</th>
								<?php /*<th>&nbsp;</th>*/?>
							</tr>
						</thead>
						<tbody>						
						<tr>
								<td width="20%">									
									<select id="courier" name="courier" class="form-control" onchange="removeError('courier');" onblur="removeError('courier');">
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
								<td width="15%"><input type="text" onblur="removeError('track_number');" id="track_number" name="track_number" class="form-control"></td>
								<td width="15%"><input type="text" onblur="removeError('vehicle_number');" id="vehicle_number" name="vehicle_number" class="form-control"></td>
								<td width="15%">
								 <select class="form-control select2me" name="representative_name" id="representative_name">
                                    <option value="">Please select</option>    
                                    @foreach($deliveryUsers as $User)
                                    <option value="{{ $User->user_id }}" mobile-no="{{ $User->mobile_no }}">{{ $User->firstname.' '.$User->lastname }}</option>
                                    @endforeach
                                </select>
								</td>
								<td width="15%"><input type="number" pattern="\d{10}" onblur="removeError('contact_num');" id="contact_num" name="contact_num" class="form-control" maxlength="10"></td>
								<?php /*<td width="15%"><a href="javascript:void(0);" id="addTracking"><i class="fa fa-plus"></i></a></td>*/?>
							</tr>
						</tbody>
						
					</table>
				</div>
			</div>

						
			<div class="row">
				
				<div class="col-md-6">
					<div class="box2">
						<div class="row">			
							<div class="col-md-12">
								<strong>Shipment Status</strong>
								<p class="text-danger" id="shippedAjaxResponse"></p>
								<select id="shipment_status" name="shipment_status" class="form-control">
									<option value="">Select Status</option>
									@if(isset($allShipmentStatusArr) && is_array($allShipmentStatusArr))
										@foreach($allShipmentStatusArr as $statusId=>$statusValue)
										<option value="{{$statusId}}">{{($statusId == '17006' ? 'SHIPPED & INVOICED' : $statusValue)}}</option>
										@endforeach
									@endif	
								</select>
							</div>
						</div>
					
						<div class="row">
							<div class="col-md-12">
								<h4>Comment</h4>
								<textarea name="shipment_comment" id="shipment_comment" class="form-control" rows="4"></textarea>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-12">
								<div class="">
									<input type="submit" id="btnShipmentSubmit" class="btn green-meadow" value="Submit">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="portlet-body">
						<div class="table-responsive">
							<h4>Comment History</h4>
							@include('Orders::comments')							
						</div>
					</div>	
				</div>
				</div>				
		</form>	

@section('script')
<style>
	.error{color:red;}
.error-style
    {
        border:1px solid #b94a48;
    }
.success-style
    {
        border:1px solid #0000ff;
    }    
</style>
   <style type="text/css"> .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
.loderholder img{ position: absolute; top:50%;left:50%;    }
.error{color: red;}
</style>

<script type="text/javascript">
	function getCheckedBox() {
		var checked = false;
		$("input[name='orderItems[]']").each( function () {
			   if($(this).prop('checked') == true){
				  checked = true;
				  return;
			   }
		});
		return checked;
	}

	$(document).ready(function() {
		
		$('#addTracking').click(function () {
			validateTracking();				
		});
		
		$("#track_data").on('click', '#removeTrack', function () {
			$(this).parent().parent().remove();
		});
					    
		$('#courier').change(function () {
			getCarrierServices();
		});

		$('#representative_name').on('change',function(){
            $('#contact_num').val($(this).find('option:selected').attr('mobile-no'))
        })
		
		$("#order_shipment_form").validate({
			rules: {				
				shipment_status:"required"		
			},
			
			submitHandler: function(form) {
				var form = $('#order_shipment_form');
				var status = $('#shipment_status').val();
				if(status == '17005') {
					$('#courier').attr('disabled', true);
	                $('#service_name').attr('disabled', true);
	                $('#track_number').attr('disabled', true);
	                $('#vehicle_number').attr('disabled', true);
	                $('#representative_name').attr('disabled', true);
	                $('#contact_num').attr('disabled', true);
				}
				else {
					$('#courier').attr('disabled', false);
	                $('#service_name').attr('disabled', false);
	                $('#track_number').attr('disabled', false);
	                $('#vehicle_number').attr('disabled', false);
	                $('#representative_name').attr('disabled', false);
	                $('#contact_num').attr('disabled', false);
				}
				
				$('.loderholder').show();
				   $.ajax({
							url: form[0].action,
							type: "POST",
							data: form.serialize(),
							dataType: 'json',
							success: function(data) {
								if(data.status == 200) {
									$('.loderholder').show();
									$('#shippedAjaxResponse').removeClass('text-danger').addClass('text-success').html(data.message);									
                                    window.location.href='/salesorders/shipmentdetail/'+data.gds_ship_grid_id;
								}
								else {
									$('.loderholder').hide();
									$('#shippedAjaxResponse').removeClass('text-success').addClass('text-danger').html(data.message);
								}
							},
							error:function(response){
								$('.loderholder').hide();
								$('#shippedAjaxResponse').html('Unable to saved comment');
							}
					}); 
			}
		});
	});	
</script>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>

@stop				
@else
<div>&nbsp;</div>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger">
		  {{$notifyMessage}}
		</div>
	</div>
</div> 
@endif 

