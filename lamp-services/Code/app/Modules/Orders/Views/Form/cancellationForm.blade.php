@if(count($productArr))
<form id="order_cancel_form" action="/salesorders/cancelItem" method="POST">
<div class="tabbable-line">                    
	<div class="row">
		<div class="col-md-12 col-sm-12">
			<div class="portlet">
				<h4>Product Description</h4>
				<div class="portlet-body">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-striped">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th> SKU# </th>
									<th> Product Name </th>
									<th> MRP </th>
									<th> Unit Price </th>
									<th> Ordered QTY </th>
									<th> Shipped QTY </th>
									<th> Invoiced QTY </th>
									<th> Cancelled QTY </th>
									<th> Cancel QTY </th>
									<th> Cancel Reason </th>
								  </tr>
							</thead>
							<tbody>
								@foreach($productArr as $product)
								<tr>
									<td>
										<input type="checkbox" id="chk{{$product->product_id}}" name="orderItems[]" value="{{$product->product_id}}">
									</td>
									<td>{{$product->sku}}</td>
									<td>{{$product->pname}} {{!empty($product->seller_sku) ? '('.$product->seller_sku.')' : ''}}</td>
									<td>{{$product->mrp}}</td>
									<td>{{number_format($product->price,2)}}</td>
									<td>{{(int)$product->qty}}</td>
									<td>{{(int)$product->shippedQty}}</td>
									<td>{{(int)$product->invoicedQty}}</td>
									<td>{{(int)$product->cancelled_qty}}</td>
									<td>
                                    	<input type="number" class="form-control cancelqty" size="3" min="1" max="{{$product->avail_qty}}" value="{{$product->avail_qty}}" name="available_qty[{{$product->product_id}}]" id="{{$product->product_id}}">
									</td>
									<td>
										<select class="form-control" name="cancelReason[{{$product->product_id}}]">
		                                <option value="">Select</option>
		                                @foreach ($cacelReasonArr as $key => $value)
		                                <option value="{{$key}}">{{$value}}</option>
		                                @endforeach
			                            </select>
									</td>
									<input type="hidden" value="{{(int)$product->qty}}" name="item_qty[{{$product->product_id}}]">
									<input type="hidden" value="{{$product->sku}}" name="item_sku[{{$product->product_id}}]">
								 </tr>
								@endforeach     
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="_method" value="POST">
			<input type="hidden" name="gds_order_id" id="cancel_order_id" value="{{$orderdata->gds_order_id}}">
			<div class="box2">
				<strong>Order Status</strong>
				<div class="form-group">
					<select id="cancel_status" name="cancel_status" class="form-control">
						<option value="">Select Status</option>
						@if(isset($commentStatusArr) && is_array($commentStatusArr))
							@foreach($commentStatusArr as $statusId=>$statusValue)
							<option value="{{$statusId}}">{{$statusValue}}</option>
							@endforeach
						@endif	
					</select>
				</div>
				
				<div class="form-group">
					<p class="text-danger" id="cancelAjaxResponse"></p>
					<textarea class="form-control" rows="3" id="cancel_comment" name="order_comment" placeholder="Enter your comment"></textarea>
				</div>

				<div class="form-group">
					<input type="submit" id="btnCancelSubmit" class="btn green-meadow" value="Submit">
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
	</div>
</form>
@else
<div class="row">
	<div>&nbsp;</div>
	<div class="col-md-12 col-sm-12">
		<div class="alert alert-danger">
		  {{$notifyMessage}}
		</div>
	</div>
</div>
@endif
