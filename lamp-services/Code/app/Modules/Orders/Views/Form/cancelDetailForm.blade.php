 @if(count($products)) 
	<div class="row">
		<div class="col-md-12 col-sm-12">
			<div class="portlet">
				<h4>Product Description</h4>
				<div class="portlet-body">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-striped">
							<thead>
								<tr>
									<th> SKU# </th>
									<th> Product Name </th>
									<th> MRP </th>
									<th> Unit Price </th>
									<th> QTY </th>
									<th> Amount </th>
									<th> Reason </th>
								  </tr>
							</thead>
							<tbody>
								@foreach($products as $product)
								<tr>
									<td>{{(isset($productArr[$product->product_id]->sku) ? $productArr[$product->product_id]->sku : '')}}</td>
									<td>{{(isset($productArr[$product->product_id]->pname) ? $productArr[$product->product_id]->pname : '')}} {{!empty($productArr[$product->product_id]->seller_sku) ? '('.$productArr[$product->product_id]->seller_sku.')' : ''}}</td>
									<td>{{(isset($productArr[$product->product_id]->mrp) ? $productArr[$product->product_id]->mrp : '')}}</td>
									<td>{{$product->unit_price}}</td>
									<td>{{(int)$product->qty}}</td>
									<td>{{$product->total_price}}</td>
									<td>{{isset($cacelReasonArr[$product->cancel_reason_id]) ? $cacelReasonArr[$product->cancel_reason_id] : ''}}</td>
								</tr>
								@endforeach     
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php /*
	<div class="row">
		<div class="col-md-12">
			<div class="portlet-body">
				<div class="table-responsive">
					<h4>Comment History</h4>
					@include('Orders::comments')
				</div>
			</div>	
		</div>    
	</div>*/?>
@else
<div class="row">
	<div class="col-md-12 col-sm-12">No product for cancellation</div>
</div>
@endif
