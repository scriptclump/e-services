	
<form action="" class="" id="freebie_configuration" method="POST">
<input type="hidden" name="_token" id="csrf-token1" value="{{ Session::token() }}" />
<div class="modal fade modal-scroll" id="addFreeBie" tabindex="-1" role="basic" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<h4 class="modal-title">FREEBIE CONFIGURATION</h4>
</div>
<div class="modal-body model_style">

<div class="row">
<div class="col-md-6">
<div class="form-group err">
	<label>MPQ</label>
<input type="text" class="form-control"  name="freebie_mpq" id="freebie_mpq"/>
<input type="hidden" class="form-control"  value="{{$product_id}}" name="main_product_id" id="main_product_id"/>
<input type="hidden" class="form-control"   name="freebie_id" id="freebie_id"/>


</div>
</div>

<div class="col-md-6">
<div class="form-group err">
<label>Free Product Name</label>
<select  class="form-control select2me "  name="freeBieProduct_id" id="freeBieProduct_id">
	<option value="">Please select ...</option>
</select>
</div>
</div>



</div>
<div class="row">
<div class="col-md-6">
<div class="form-group err">
<label>Free Qty</label>
<input type="number" min="0" class="form-control" name="freeBieQty" id="freeBieQty" />
</div>
</div>


<div class="col-md-6">
<div class="form-group err">
<label>Free  Description</label>
<textarea class="form-control" rows="2" id="freebie_product_description" name="freebie_product_description"></textarea>
</div>
</div>


</div>
<div class="row">

<div class="col-md-6">
	<div class="form-group err">
<label>State Name</label>

<select  id="freebie_state_id" name="freebie_state_id" class="form-control select2me">
	<option value=''>Please select ...</option>
	@foreach($getZoneData as $warehouseKey)
	<option value="{{$warehouseKey['zone_id']}}">{{$warehouseKey['name']}}</option>
	@endforeach
</select>
</div>
</div>
<div class="col-md-6">
	<div class="form-group err">
<label>WareHouse Name</label>
<select  id="freebie_warehouse_id" name="freebie_warehouse_id" class="form-control select2me">
	<option>Please select ...</option>

</select>
</div>
</div>


</div>


<div class="row">
<div class="col-md-6">
<div class="form-group err">
<label>Start Date</label>
<div class="input-icon right" style="width: 100%">
                <i class="fa fa-calendar" style="line-height: 5px"></i>
                <input type="text" class="form-control" name="freebie_start_date" id="freebie_start_date" >
            </div>
</div>
</div>
<div class="col-md-6">
<div class="form-group err">
<label>End Date</label>

<div class="input-icon right" style="width: 100%">
                <i class="fa fa-calendar" style="line-height: 5px"></i>
                <input type="text" class="form-control" name="freebie_end_date" id="freebie_end_date" >
</div>
</div>
</div>
</div>

<div class="row">

<div class="col-md-6">
<div class="form-group err">
<label>Enable Stock Limit</label>
<label class="switch "><input class="switch-input vr_status4"  type="checkbox" check="false" id="enable_stock_limit" name="enable_stock_limit" ><span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>
</div>
</div>

<div class="col-md-6">
<div class="form-group err">
<label>Stock Limit	</label>
<input type="text" class="form-control" name="freebie_stock_limit" value="0" disabled="disabled" id="freebie_stock_limit"/>
</div>
</div>


</div>
</div>
<div class="modal-footer">
<div class="row">
<div class="col-md-12 text-center">
<button type="submit" class="btn green-meadow save_freebie" >Save</button>
</div>
</div>
</div>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
</form>
