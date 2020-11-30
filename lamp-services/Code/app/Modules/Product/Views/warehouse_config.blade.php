	
<form action="" class="" id="wh_bin_configuration" method="POST">
<input type="hidden" name="_token" id="csrf-token1" value="{{ Session::token() }}" />
<div class="modal fade modal-scroll" id="add_bin_config" tabindex="-1" role="basic" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<h4 class="modal-title">BIN CONFIGURATION</h4>
</div>
<div class="modal-body model_style">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group err">
				<label>Wareshouse Name</label>
				<input type="hidden" id="edit_wh_id" value=''>
				 <select class="form-control" id="wh_id" name="wh_id">
				 	<option value="0">Please select...</option>
				 	@if(!empty($warehouse_data))
					@foreach($warehouse_data as $wh_value)
						<option value="{{$wh_value['le_wh_id']}}">{{$wh_value['lp_wh_name']}}</option>
					@endforeach
					@endif
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group err">
				<label>Bin Type</label>
				<select class="form-control" id="bin_type" name="bin_type">
				 	<option value="0">Please select...</option>
				 	@if(!empty($bin_type))
					@foreach($bin_type as $bin_value)
						<option value="{{$bin_value['bin_type_dim_id']}}">{{$bin_value['bin_dim_name']}}</option>
					@endforeach
					@endif
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group err">
				<label>Pack Type</label>
				<select class="form-control" id="wh_pack_type" name="wh_pack_type">
				 	<option value="0">Please select...</option>
				 	@if(!empty($packageLevel))
						@foreach($packageLevel as $packageValue)
							<option value="{{$packageValue->value}}">{{$packageValue->name}}</option>
						@endforeach
					@endif
				</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group err">
				<label>Min Capacity</label>
				<input type="number" class="form-control" min='1'  name="pro_min_capacity" id="pro_min_capacity"/>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group err">
				<label>Max Capacity</label>
				<input type="number" min='2' class="form-control"  name="pro_max_capacity" id="pro_max_capacity"/>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
<div class="row">
<div class="col-md-12 text-center">
<button type="submit" class="btn green-meadow save_wh_bin_config" >Save</button>
</div>
</div>
</div>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
</form>
