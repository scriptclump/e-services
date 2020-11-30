@if(count($shipmentProductArr))
<form id="order_shipment_form" action="/salesorders/updateShipment" method="POST">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="_method" value="POST">
<input type="hidden" name="shiment_id" id="shiment_id" value="{{$shipmentProductArr[0]->gds_ship_grid_id}}">
<input type="hidden" name="order_id" id="order_id" value="{{$shipmentProductArr[0]->gds_order_id}}">
<input type="hidden" name="status_id" id="status_id" value="{{$shipmentProductArr[0]->status_id}}">
  <div style="display:none;" id="shippedAjaxResponse" class="alert alert-success">
 </div>

<div class="row"> <div class="col-lg-12 marbot">
<?php /*	@if($shipmentProductArr[0]->status_id == '17005') 
    <div class="tools pull-right">
    <a type="button" class="btn green-meadow" id="generateInvoice" href="#">GENERATE INVOICE</a></div>
	@endif*/?> 
    </div> 
   
    <div class="col-md-12  ">
    <table class="table table-bordered thline">
        <thead> <tr><th> Billing Address </th><th> Shipping Address </th></tr> </thead>  
       
       
<tbody>
    <tr> <td> 
		@if(is_object($billing))	 
			{{$billing->fname}} {{$billing->mname}} {{$billing->lname}}<br>
			{{$billing->addr1}} {{$billing->addr2}}<br>
			{{$billing->city}}, {{$billing->state_name}}, {{$billing->country_name}}, {{$billing->postcode}}<br>
			T: {{$billing->telephone}} @if(!empty($billing->mobile))| M: {{$billing->mobile}}@endif<br>
		@endif	
		 </td> 
		 
        <td> 
		@if(is_object($shipping))	 
			{{$shipping->fname}} {{$shipping->mname}} {{$shipping->lname}}<br>
			{{$shipping->addr1}} {{$shipping->addr2}}<br>
			{{$shipping->city}}, {{$shipping->state_name}}, {{$shipping->country_name}}, {{$shipping->postcode}}<br>
			T: {{$shipping->telephone}}@if(!empty($shipping->mobile)) | M: {{$shipping->mobile}}@endif<br>
		@endif	
		 </td> </tr>    
     
</tbody>
</table>
   
    </div>   
    
    
	
</div>

<div class="row">
	<div class="col-md-12">
		<h4>Product Description</h4>
		<table class="table table-bordered thline table-scrolling">
		<thead>
			<tr>
				<th>SKU</th>
				<th>Product Name</th>
				<th>MRP</th>
				<th>Shipped Qty</th>
				<th>Comment</th>
			</tr>
		</thead>
		<tbody>
			@foreach($shipmentProductArr as $product)
			<tr class="odd gradeX">
				<td>{{(isset($productArr[$product->product_id]->sku) ? $productArr[$product->product_id]->sku : '')}}</td>
				<td>{{(isset($productArr[$product->product_id]->pname) ? $productArr[$product->product_id]->pname : '')}}</td>
				<td>{{(isset($productArr[$product->product_id]->symbol) ? $productArr[$product->product_id]->symbol : 'Rs.')}} {{(isset($productArr[$product->product_id]->mrp) ? $productArr[$product->product_id]->mrp : '0.0')}}</td>
				
				<td>{{(isset($product->shippedQty) ? (int)$product->shippedQty : '')}}</td>
				<td>{{$product->comment}}</td>
			</tr>
			@endforeach
		</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-12">					
		<h4>Tracking Information</h4>
		<table class="table table-bordered thline table-scrolling" id="track_data">
			<thead>
				<tr>
					<th width="15%">Carrier</th>
					<th width="15%">Service Name</th>
					<th width="15%">Tracking Number</th>
					<th width="15%">Vehicle Number</th>
					<th width="15%">Representative Name</th>
					<th width="15%">Contact Number</th>
					<th width="5%">&nbsp;</th>
				</tr>
			</thead>
			@if(is_array($shipmentTrackArr) && count($shipmentTrackArr) >0)
			@foreach($shipmentTrackArr as $shipmentTrack)
			<tr>
				<td>{{isset($carriersArr[$shipmentTrack->ship_service_id]) ? $carriersArr[$shipmentTrack->ship_service_id] : ''}}</td>
				<td>{{$shipmentTrack->ship_method}}</td>
				<td>{{(isset($shipmentTrack->tracking_id) ? $shipmentTrack->tracking_id : '')}}</td>
				<td>{{$shipmentTrack->vehicle_number}}</td>
				<td>{{$shipmentTrack->Reps_Name}}</td>
				<td>{{$shipmentTrack->contact_number}}</td>
				<td width="15%">&nbsp;</td>
			</tr>
			@endforeach
			@endif			
			<?php /*<tr>
					<td width="20%">									
						<select id="courier" name="courier" class="form-control" onchange="removeError('courier');" onblur="removeError('courier');">
							<option value="">Select</option>
							@foreach ($carriersArr as $carrier_id => $carrier)
							  <option value="{{$carrier_id}}">{{$carrier}}</option> 
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
					<td width="15%"><input type="text" onblur="removeError('representative_name');" id="representative_name" name="representative_name" class="form-control"></td>
					<td width="15%"><input type="number" pattern="\d{10}" maxlength="10" onblur="removeError('contact_num');" id="contact_num" name="contact_num" class="form-control"></td>
					<td width="15%"><a href="javascript:void(0);" id="addTracking"><i class="fa fa-plus"></i></a></td>
			</tr>*/?>				
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-6"> <div class="portlet-fit box2">
   <div class="row">
		<div class="col-md-12">
		<strong>Shipment Status</strong>
		<p class="text-danger" id="shippedAjaxResponse"></p>
		<select disabled="disabled" id="shipment_status" name="shipment_status" class="form-control">
			<option value="">Select Status</option>
			@if(isset($statusMatrixArr) && is_array($statusMatrixArr))
				@foreach($statusMatrixArr as $statusId=>$statusValue)
				<option value="{{$statusId}}">{{$statusValue}}</option>
				@endforeach
			@endif	
		</select>
	</div></div>
	<div class="row">
		<div class="col-md-12">
			<h4>Comment</h4>
			<textarea disabled="disabled" name="shipment_comment" id="shipment_comment" class="form-control" rows="4"></textarea>
		</div>
	</div>	
	<br>
	<div class="row">
	<div class="col-md-12">
		 
			<input disabled="disabled" type="submit" id="btnShipmentSubmit" class="btn green-meadow" value="Submit">
		 
	</div>
</div>	
	</div></div>					
	<div class="col-md-6">
		<div class="portlet-body">
			<div class="table-responsive">
                <h4><strong>Comment History</strong></h4>
				@include('Orders::comments')					
			</div>
		</div>	
	</div>
</div>

@section('script')
<style>
.error-style
    {
        border:1px solid #b94a48;
    }
.success-style
    {
        border:1px solid #0000ff;
    }    
</style>
<script type="text/javascript">

	$(document).ready(function() {

		$('#generateInvoice').click(
			function () {
				$('.loderholder').show();
				$.ajax({
						url: '/salesorders/generateinvoice',
						type: "POST",
						data: {"shipid":$('#shiment_id').val(), '_token' : $('#csrf_token').val()},
						dataType: 'json',
						success: function(data) {
							if(data.status == 200) {
								$('.loderholder').show();
								$('#shippedAjaxResponse').removeClass('text-danger').addClass('text-success').html(data.message);
								window.setTimeout(function(){location.reload()},2000);
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
		);

		$('#addTracking').click(function () {				
			var carrier = $('#courier').val();
			var service_name = $('#service_name').val();
			var carrier_name = $('#courier option:selected').text();			
			var service_text = $('#service_name option:selected').text();			
			var track_number = $('#track_number').val();			
			var vehicle_number = $('#vehicle_number').val();
			var representative_name = $('#representative_name').val();
			var contact_num = $('#contact_num').val();
			var isValid = 0;
			
			$('#courier').addClass('error-style');
			$('#service_name').addClass('error-style');
			$('#track_number').addClass('error-style');			
			
			if(carrier_name == 'Self Shipment') {
				$('#track_number').removeClass('error-style');
				$('#vehicle_number').addClass('error-style');
				$('#representative_name').addClass('error-style');
				$('#contact_num').addClass('error-style');
			}
			
			if(carrier != '' && service_name != '' && track_number != '') {				
				isValid = 1;
			}
			else if(carrier_name == 'Self Shipment' && vehicle_number != '' && representative_name != '' && contact_num != '' && validatePhone(contact_num) == true) {				
				isValid = 1;
			}
			
			$('input[name^="tracking_id"]').each(function() {
			    if(track_number == $(this).val()){
			    	isValid = 0;
			    	return;
			    }
			});			

			$('input[name^="track_numbers"]').each(function() {
			    if(track_number!='' && track_number == $(this).val()){
			    	isValid = 0;
			    	return;
			    }
			});
			

			if(isValid) {
				$('#courier').removeClass('error-style').addClass('success-style');
				$('#service_name').removeClass('error-style').addClass('success-style');
				$('#track_number').removeClass('error-style').addClass('success-style');

				$('#vehicle_number').removeClass('error-style').addClass('success-style');
				$('#representative_name').removeClass('error-style').addClass('success-style');
				$('#contact_num').removeClass('error-style').addClass('success-style');
				
				$('#track_data').append('<tr><td><input type="hidden" name="carriers[]" value="'+carrier+'">'+carrier_name+'</td><td><input type="hidden" name="services[]" value="'+service_name+'">'+service_text+'</td><td><input type="hidden" name="track_numbers[]" value="'+track_number+'">'+track_number+'</td><td><input type="hidden" name="vehicle_numbers[]" value="'+vehicle_number+'">'+vehicle_number+'</td><td><input type="hidden" name="representatives[]" value="'+representative_name+'">'+representative_name+'</td><td><input type="hidden" name="contacts[]" value="'+contact_num+'">'+contact_num+'</td><td><a href="javascript:void(0)" id="removeTrack">-</a></td></tr>');

				$('#courier').val('').removeClass('success-style');
				$('#service_name').val('').removeClass('success-style');		
				$('#track_number').val('').removeClass('success-style');			
				$('#vehicle_number').val('').removeClass('success-style');
				$('#representative_name').val('').removeClass('success-style');
				$('#contact_num').val('').removeClass('success-style');
				$('#tracking-error').hide();

			}			
		});
		
		$("#track_data").on('click', '#removeTrack', function () {
			$(this).parent().parent().remove();
		});
			    
		$('#courier').change(function () {
			getCarrierServices();
		});
		
		$("#order_shipment_form").validate({
			rules: {
				//shipment_status:"required"		
			},
			submitHandler: function(form) {				
				var form = $('#order_shipment_form');
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
									window.setTimeout(function(){location.reload()},2000);
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
    <style type="text/css"> .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
.loderholder img{ position: absolute; top:50%;left:50%;    }
.error{color: red;}
</style>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop
</form>		
@endif 
