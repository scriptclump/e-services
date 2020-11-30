@if(count($returnProductArr))
<form id="order_return_form" action="/salesorders/updateReturn" method="POST">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="_method" value="POST">
	<input type="hidden" name="return_id" id="return_id" value="{{$returnProductArr[0]->return_grid_id}}">
	<input type="hidden" name="order_id" id="order_id" value="{{$returnProductArr[0]->gds_order_id}}">
	<input type="hidden" name="status_id" id="status_id" value="{{$returnProductArr[0]->return_status_id}}">
	<input type="hidden" name="le_wh_id" id="le_wh_id" value="{{$orderdata->le_wh_id}}">
	<input type="hidden" name="return_order_code" id="return_order_code" value="{{$returnProductArr[0]->return_order_code}}">
	<div class="row"> <div class="col-lg-12 marbot"> 
		<div class="tools pull-right">
			<?php /*<a type="button" class="btn green-meadow" href="/salesorders/createinvoice/{{$shipmentProductArr[0]->gds_order_id}}/{{$shipmentProductArr[0]->gds_ship_grid_id}}">CREATE INVOICE</a>*/?></div></div> 

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
				<table class="table table-bordered thline">
					<tbody>
						<tr class="odd gradeX">
							<td class="col-md-6"><strong>Current Return Status</strong></td>
							<input type="hidden" name="ret_status" id="ret_status" value="{{$statusMatrixArr['status']}}">
							@if(isset($returnProductArr))

							<td class="col-md-6">{{$returnProductArr[0]->return_stat}} {{$transitMessage}}
								<?php

									$mainstatusValue = $returnProductArr[0]->return_stat;
									$mainstatusId = $returnProductArr[0]->return_status_id;
								?>
	
							</td>


							@endif
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<strong>Returned Product</strong>
				<table class="table table-bordered thline table-scrolling">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>SKU</th>
							<th>Product Name</th>
							<th>Returned Qty</th>
							<th>Approved Qty</th>
							<th>Quaratined Qty</th>
							<th>DIT Qty</th>
							<th>Missing QTY</th>
							<th>Excess QTY</th>
							<th>Return Reason</th>
						</tr>
					</thead>
					<tbody>
						@foreach($returnProductArr as $product)
						<tr class="odd gradeX {{(isset($product->is_extra) && $product->is_extra==1) ? 'returnextra' : ''}}">
							<td>&nbsp;</td>
							<td>{{$product->sku}}</td>
							<td>{{$product->product_title}}</td>
							<td>{{$product->qty}} <input type="hidden" name="return_qty[{{$product->product_id}}]" id="return_qty{{$product->product_id}}"  value="{{$product->qty}}"> </td>
							@if(isset($returnProductArr[0]->return_status_id) && $returnProductArr[0]->return_status_id == '57066' || $statusMatrixArr['status'] == 0)
								<td>@if(isset($product->approved_quantity) && !empty($product->approved_quantity)){{$product->approved_quantity}}@else 0 @endif</td>
								<td>@if(isset($product->quarantine_qty) && !empty($product->quarantine_qty)){{$product->quarantine_qty}}@else 0 @endif</td>
								<td>@if(isset($product->dit_qty) && !empty($product->dit_qty)){{$product->dit_qty}}@else 0 @endif</td>
								<td>@if(isset($product->dnd_qty) && !empty($product->dnd_qty)){{$product->dnd_qty}}@else 0 @endif</td>
								<td>@if(isset($product->excess_qty) && !empty($product->excess_qty)){{$product->excess_qty}}@else 0 @endif</td>
                            @else
								<td> <input type="number"  min="0" max="{{$product->qty}}" value="{{$product->approved_quantity}}"  name="apprvd_qty[{{$product->product_id}}]" id="apprvd_qty{{$product->product_id}}" onchange="getGoodQty({{$product->product_id}})"></td>
                                <td><input type="number"  min="0" max="{{$product->qty}}" value="{{$product->quarantine_qty}}" name="quaratined_qty[{{$product->product_id}}]" id="quaratined_qty{{$product->product_id}}" onchange="getBadQty({{$product->product_id}})">
                                </td>
                                 <td>
                                    <input type="number"  min="0" max="{{$product->qty}}" value="{{$product->dit_qty}}" name="dit_qty[{{$product->product_id}}]" id="dit_qty{{$product->product_id}}" class="dit_qty" onchange="getDitQty({{$product->product_id}})">
                                </td>
                                <td >
                                    <input type="number"  min="0" max="{{$product->qty}}" value="{{$product->dnd_qty}}" name="dd_qty[{{$product->product_id}}]" id="dd_qty{{$product->product_id}}" class="dd_qty" onchange="getDDQty({{$product->product_id}})">
                                </td>
                                <td >
                                    <input type="number"   max="{{$product->qty}}" value="{{$product->excess_qty}}" name="excess_qty[{{$product->product_id}}]" id="excess_qty{{$product->product_id}}" class="excess_qty"  style="width:50px;">
                                </td>
                                
							@endif
								<td>@if(isset($returnReason[$product->return_reason_id]))
										{{$returnReason[$product->return_reason_id]}}
									@endif
								</td>
						</tr>
						@endforeach
	 				</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6"> <div class="portlet-fit box2">
				<div class="row">
					<div class="col-md-12">
						<strong>Return Status</strong>
						<p class="text-danger" id="returnAjaxResponse"></p>
						<?php if($viewOnly != 0) { ?>
							@if(isset($statusMatrixArr) && $statusMatrixArr['status'] ==1)
							<select id="return_status" name="return_status" class="form-control">
								<option value="">Select Status</option>
								
								<option value="{{$statusMatrixArr['data'][0]['nextStatusId']}},{{$statusMatrixArr['data'][0]['isFinalStep']}}">{{$statusMatrixArr['data'][0]['nextStatus']}}</option>
								<input type="hidden" name="currentStatusID" value="{{$statusMatrixArr['currentStatusId']}}">
	                            <input type="hidden" name="nextStatusId" value="{{$statusMatrixArr['data'][0]['nextStatusId']}}">
							</select>
							@else
							<select id="return_status" disabled="true" name="return_status" class="form-control">
								<option value="">Select Status</option>
							</select>
							@endif
						<?php } else{ ?>

							<select id="return_status" disabled="true" name="return_status" class="form-control">
							<option value="">Select Status</option>
							</select>
						<?php }?>
					</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<h4>Comment</h4>
							@if(isset($statusMatrixArr) && $statusMatrixArr['status'] ==1)
							<textarea name="return_comment" id="return_comment" class="form-control" rows="4"></textarea>
							@else
							<textarea disabled="true" name="return_comment" id="return_comment" class="form-control" rows="4"></textarea>
							@endif
						</div>
					</div>	
					<br>
					<div class="row">
						<div class="col-md-12">

							<input type="submit" id="btnReturnSubmit" class="btn green-meadow" value="Submit">

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
				.returnextra
				{
					background: lightblue;
				}
				.loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }    
			</style>
			<script type="text/javascript">

				disableDropdown();
				function disableDropdown(){
					var productArr = <?php echo json_encode($returnProductArr); ?>;
					var statusId = 57066;
					var ret_status = $('#ret_status').val();
					console.log(statusId);
					if(productArr[0].return_status_id == statusId || ret_status != 1){
						$('#return_status').prop('disabled', true);	
						$('#return_comment').prop('disabled', true);					
						$('#btnReturnSubmit').prop('disabled', true);
					}
					else{
						$('#return_status').prop('disabled', false);    
					}
				}

				$(document).ready(function() {
					$("#order_return_form").validate({
						rules: {
			//shipment_status:"required"		
		},
		submitHandler: function(form) {	
			if (confirm('Are you Sure! You want to Save?')) {
				$('.loderholder').show();
				$('#btnReturnSubmit').prop('disabled', true);
				var form = $('#order_return_form');
				$.ajax({
					url: form[0].action,
					type: "POST",
					data: form.serialize(),
					dataType: 'json',
					success: function(data) {
						if(data.status == 200) {
							$('.loderholder').show();
							$('#returnAjaxResponse').removeClass('text-danger').addClass('text-success').html(data.message);
							window.setTimeout(function(){location.reload()},2000);
						}
						else {
							$('.loderholder').hide();
							$('#returnAjaxResponse').removeClass('text-success').addClass('text-danger').html(data.message);
						}
					},
					error:function(response){
						$('#returnAjaxResponse').html('Unable to save status');
					}
				}); 
			};				
		}
	});
});

 function getGoodQty(productid){
        var goodqty = parseInt($("#apprvd_qty" + productid).val());
        var retqty = parseInt($("#return_qty" + productid).val());
        var badqty = parseInt($("#quaratined_qty" + productid).val());
        var ddqty = parseInt($("#dd_qty" + productid).val());
        var ditqty = parseInt($('#dit_qty' + productid).val());
        if(isNaN(goodqty) == true){
            var goodqty = 0;
            var badqty = retqty;
            var ditqty = 0;
            var ddqty = 0;
        }else if(goodqty > retqty || goodqty < 0){
            var badqty = badqty;
            var ditqty = ditqty;
            var ddqty = ddqty; 
        }else{
            var badqty = retqty-goodqty-(ditqty+ddqty);
            var ditqty = retqty-badqty-(goodqty+ddqty);
            var ddqty = retqty-badqty-(goodqty+ditqty);
            if(badqty <0){
                var badqty = 0;
                var ditqty = retqty-(goodqty+ddqty);
                var ddqty = retqty-(goodqty+ditqty);

            }
            if(ditqty<0){
                var ditqty = 0;
                var badqty = 0;
                var ddqty = retqty - goodqty;
            }
            


        }   

        $("#apprvd_qty" + productid).val(goodqty);
        $('#dit_qty' + productid).val(ditqty);
        $("#quaratined_qty" + productid).val(badqty);
        $("#dd_qty" + productid).val(ddqty);

    }
    function getBadQty(productid){
        var badqty = parseInt($("#quaratined_qty" + productid).val());
        var retqty = parseInt($("#return_qty" + productid).val());
        var goodqty = parseInt($("#apprvd_qty" + productid).val());
        var ditqty = parseInt($("#dit_qty"+ productid).val());
        var ddqty = parseInt($("#dd_qty"+ productid).val());

        if(isNaN(badqty) == true){
            var goodqty = 0;
            var badqty = retqty;
            var ditqty = 0;
            var ddqty = 0; 
        }else if(badqty > retqty){
            var goodqty = goodqty;
            var ditqty = ditqty;
            var ddqty = ddqty;
        }else{
            var goodqty = retqty-badqty-(ditqty+ddqty);
            var ditqty = retqty-badqty-(goodqty+ddqty);
            var ddqty = retqty-badqty-(goodqty+ditqty);
            if(goodqty <0){
                var goodqty = 0;
                var ditqty = retqty-(badqty+ddqty);
                var ddqty = retqty-(badqty+ditqty);

            }
            if(ditqty<0){
                var ditqty= 0;
                var badqty= 0;
                var ddqty = retqty - badqty;
            }
            


        }


        
        $("#apprvd_qty" + productid).val(goodqty);
        $('#dit_qty' + productid).val(ditqty);
        $("#quaratined_qty" + productid).val(badqty);
        $("#dd_qty" + productid).val(ddqty);

    }

function getDitQty(productid){
    var badqty = parseInt($("#quaratined_qty" + productid).val());
    var retqty = parseInt($("#return_qty" + productid).val());
    var goodqty = parseInt($("#apprvd_qty" + productid).val());
    var ditqty = parseInt($("#dit_qty" + productid).val());
    var ddqty = parseInt($("#dd_qty" + productid).val());
    if(isNaN(ditqty) == true){
        var goodqty = retqty;
        var badqty = 0;
        var ditqty = 0;
        var ddqty = 0;
    }else if(ditqty > retqty || ditqty < 0 ){
        var goodqty = goodqty;
        var badqty = badqty;
        var ddqty = ddqty;
    }else{
        var badqty = retqty-ditqty-(goodqty+ddqty);
        var goodqty = retqty-badqty-(ditqty+ddqty);
        var ddqty = retqty-(goodqty+badqty+ditqty);
        if(badqty <0){
            var badqty = 0;
            var goodqty = retqty-(ditqty+ddqty);
            var ddqty = retqty-(goodqty+ditqty);

        }
        if(goodqty<0){
            var goodqty=0;
            var badqty=0;
            var ddqty =retqty - ditqty;
        }
        


    }
    $("#apprvd_qty" + productid).val(goodqty);
    $('#dit_qty' + productid).val(ditqty);
    $("#quaratined_qty" + productid).val(badqty);
    $("#dd_qty" + productid).val(ddqty);

}
function getDDQty(productid){
    var badqty = parseInt($("#quaratined_qty" + productid).val());
    var retqty = parseInt($("#return_qty" + productid).val());
    var goodqty = parseInt($("#apprvd_qty" + productid).val());
    var ditqty = parseInt($("#dit_qty" + productid).val());
    var ddqty = parseInt($("#dd_qty" + productid).val());
    if(isNaN(ddqty) == true){
        var goodqty = retqty;
        var badqty = 0;
        var ditqty = 0;
        var ddqty = 0; 
    }else if(ddqty > retqty || ddqty < 0 ){
        var goodqty = goodqty;
        var badqty = badqty;
        var ditqty = ditqty;
    }else{
        var badqty = retqty-ddqty-(goodqty+ditqty);
        var goodqty = retqty-badqty-(ditqty+ddqty);
        var ditqty = retqty-(goodqty+badqty+ddqty);
        if(badqty <0){
            var badqty = 0;
            var goodqty = retqty-(ddqty+ditqty);
            var ditqty = retqty-(goodqty+ddqty);

        }
        if(goodqty<0){
            var goodqty = 0;
            var badqty = 0;
            var ditqty = retqty - ddqty;
        }
       

    }
    $("#apprvd_qty" + productid).val(goodqty);
    $('#dit_qty' + productid).val(ditqty);
    $("#quaratined_qty" + productid).val(badqty);
    $("#dd_qty" + productid).val(ddqty);

}


	</script>
	@stop
</form>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>		
@endif 
