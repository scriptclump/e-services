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
									<th> QTY </th>
								  </tr>
							</thead>
							<tbody>
								@foreach($products as $product)
								<tr>
									<td>{{$product->sku}}</td>
									<td>{{$product->product_name}} {{!empty($productArr[$product->product_id]->seller_sku) ? '('.$productArr[$product->product_id]->seller_sku.')' : ''}}</td>
									<td>{{(int)$product->quantity}}</td>
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
		<div class="col-md-12">
			<div class="portlet-body">
				<div class="table-responsive">
					<h4>Comment History</h4>
					@include('Orders::comments')
				</div>
			</div>	
		</div>    
	</div>
@else
<div class="row">
	<div class="col-md-12 col-sm-12">No product for cancellation</div>
</div>
@endif
