<form id="frm_sales_orders" action="" method="post">	
<div id="filters" style="display:none;">
			<div class="row">			
				<div class="col-md-3">
					<div class="form-group">
						<select name="channel" id="channel" class="form-control">
							<option value="">All Channels</option>
							@foreach ($allLegalEntityArr as $legalEntity)
							  <option value="{{$legalEntity->mp_id}}">{{$legalEntity->mp_name}}</option> 
							@endforeach
						</select>
					</div>
				</div>
			
				<div class="col-md-3">
					<div class="form-group">
						<select name="order_status_id" id="order_status_id" class="form-control">
							<option value="">All Status</option>		
							@foreach ($allStatusArr as $orderId => $orderValue)
							  <option value="{{$orderId}}">{{$orderValue}}</option> 
							@endforeach
						</select>
					</div>
				</div>
			
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id="order_id" name="order_id" class="form-control" placeholder="Order ID">
					</div>
				</div>
			
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id="channel_id" name="channel_id" value="" class="form-control" placeholder="Channel Order ID">
					</div>
				</div>		
			</div>
		
		
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<div class="input-icon right">
								<i class="fa fa-calendar"></i>
								<input type="text" class="form-control" name="order_fdate" id="order_fdate" placeholder="Order From Date">
							</div>
						</div>
						<div class="col-md-6">
							<div class="input-icon right">
							<i class="fa fa-calendar"></i>
							<input type="text" class="form-control" name="order_tdate" id="order_tdate" placeholder="Order To Date">
							</div>
						</div>
					</div>
				</div>
			</div>
		
			<div class="col-md-6">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">			
							<div class="input-icon right">
								<i class="fa fa-calendar"></i>
								<input type="text" class="form-control" name="exp_fdate" id="order_exp_fdate" placeholder="Expiry From Date">
							</div>
						</div>
						<div class="col-md-6">
							<div class="input-icon right">
								<i class="fa fa-calendar"></i>
								<input type="text" class="form-control" name="exp_tdate" id="order_exp_tdate" placeholder="Expiry To Date">
							</div>
						</div>
					</div>
				</div>
			</div>		
		</div>
		<div class="row">
			<div class="form-group">
				
				<div class="col-md-3">
					<input type="text" class="form-control" name="customer" id="customer" placeholder="Cust Name">
				</div>
				<div class="col-md-3">
                                    <select class="form-control" name="payment_method" id="payment_method">
                                        <option value="">Payment Method</option>
                                        @foreach ($paymentTypes as $value => $Name)
                                            <option value="{{$value}}">{{$Name}}</option>
                                        @endforeach
                                    </select>
				</div>
				<div class="col-md-3">
                                    <input class="form-control" name="cust_mobile" id="cust_mobile" placeholder="Cust Mobile">                                       
				</div>
				
				
				<div class="col-md-3 text-right">
					<input type="button" value="Filter" class="btn btn-success" onclick="filterOrder('{{$status}}');">
					<input type="reset" value="Reset" class="btn btn-success">
				</div>
					
			</div>				
			
		</div>
		<hr />
</div>
</form>
