	
<form action="" class="" id="package_configuration" method="POST">
<input type="hidden" name="_token" id="csrf-token1" value="{{ Session::token() }}" />
<div class="modal fade modal-scroll" id="addpacking" tabindex="-1" role="basic" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<h4 class="modal-title">PACKING CONFIGURATION</h4>
</div>
<div class="modal-body model_style">
<input type="hidden" name="edit_pack_id" id="edit_pack_id" value="">
<input type="hidden" name="pack_product_id" id="pack_product_id" value="{{$product_id}}">
<input type="hidden" name="pack_status" id="pack_status" value="">
<input type="hidden" name="pack_id" id="pack_id" value="">


<div class="row">
<div class="col-md-6">
<div class="form-group err1">
<label>Level</label>
<select  class="form-control"  name="packageLevel" id="packageLevel">
	<option value="">Please select ...</option>
@foreach($packageLevel as $packageValue)
<option value="{{$packageValue->value}}">{{$packageValue->name}}</option>
@endforeach</select>
</div>
</div>
<div class="col-md-6">
<div class="form-group err">
<label>Pack SKU Code</label>
<input type="text" class="form-control"  name="packSkuCode" id="packSkuCode"/>
</div>
</div>
</div>
<div class="row">
<div class="col-md-3">
<div class="form-group err">
<label>Eaches #</label>
<input type="number" min="0" class="form-control" name="packEaches" id="packEaches" />
<input type="hidden" value="" name="editPackEaches" id="editPackEaches">
<input type="hidden" value="" name="existedEditPackEaches" id="existedEditPackEaches">
</div>
</div>
<div class="col-md-3">
<div class="form-group err">
<label>Inner #</label>
<input type="number" min="0" class="form-control" name="packInner" id="packInner" />
</div>
</div>
<div class="col-md-2">
@if($supplier_login_permissions == 0)
<div class="form-group err">
<label>{{trans('headings.SU')}} </label>
<input type="number" min="0" class="form-control" name="packEsu" id="packEsu" />
</div>
@endif
</div>

<div class="col-md-4">
<div class="form-group err">

@if($supplier_login_permissions == 1)


@else
<label>Star </label>
<select class="form-control select2me" name="product_pack_star" id="product_pack_star">
   <option value="0">Please select</option>
    @foreach($product_star as $starValue)
    	<option value="{{$starValue->value}}">{{$starValue->name}} </option>
    @endforeach
</select>  
@endif   
</div>
</div>

</div>

<div class="row">

<div class="col-md-6">
<div class="form-group">
<label>Dimensions</label>

<div class="row">
<div class="col-md-4">
 
<input type="text" class="form-control" placeholder="Length" name="pack_lenght" id="pack_lenght" />
 
</div>
<div class="col-md-4">
 
<input type="text" class="form-control"  placeholder="Breadth" name="pack_breadth" id="pack_breadth"/>
 
</div>
<div class="col-md-4">
 
<input type="text" class="form-control" placeholder="Height" id="pack_height" name="pack_height" />
 
</div>
</div>

</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label>LBH UOM</label>
<select class="form-control" name="lbh_uom" id="lbh_uom">
	@foreach($packageLBHUOM as $valueLBH)
<option value="{{$valueLBH->value}}">{{$valueLBH->name}}</option>
	@endforeach
</select>
</div>
</div>
</div>
<div class="row">

<div class="col-md-6">
<div class="form-group err">
<label>Weight</label>
<input type="text" class="form-control" name="weight" id="weight"/>
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label>Weight UOM</label>
<select class="form-control" name="packWeightUOM" id="packWeightUOM">
@foreach($packageWeightUOM as $packUOMValue)
<option value="{{$packUOMValue->value}}">{{$packUOMValue->name}}</option>
@endforeach

</select>
</div>
</div>
</div>

<div class="row">
<div class="col-md-6">
    	<div class="form-group">
			<label class="control-label">Effective Date</label>
			<input type="text" class="form-control" name="effective_date" id="effective_date" autocomplete="off">
		</div>

</div>
<div class="col-md-6">
<div class="form-group err">
<label>Packing Material</label>
<input type="text" class="form-control" name="packingMeterial" id="packingMeterial"/>
</div>
</div>
</div>
<div class="row">
	<div class="col-md-6">	            
            <div class="form-group err">
<label>Stack Height</label>
<input type="text" class="form-control" name="stackHeight" id="stackHeight"/>
</div>    
	</div>
<div class="col-md-6">
<div class="form-group err">
<label>Pallete Capacity</label>
<input type="number" min="0" class="form-control" name="palleteCapacity" id="palleteCapacity" />
</div>
</div>
</div>
<div class="row">
<div class="col-md-3">
<div class="form-group">
	
<label>Palletization</label>
<label class="switch "><input class="switch-input vr_status4"  type="checkbox" check="false" id="palletization" name="palletization" ><span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>
<!-- <input type="checkbox" class="form-control" name="palletization" id="palletization"/> -->
</div>
</div>
<div class="col-md-3">
<div class="form-group">

	
<label>Is Sellable</label>
<label class="switch "><input class="switch-input vr_status4"  type="checkbox" check="false" id="is_sellable" name="is_sellable" ><span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>
<!-- <input type="checkbox" class="form-control" name="palletization" id="palletization"/> -->
</div>
</div>
<div class="col-md-3">
<div class="form-group">

	
<label>Is Cratable</label>
<label class="switch "><input class="switch-input vr_status4"  type="checkbox" check="false" id="is_cratable" name="is_cratable" ><span class="switch-label " data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>
<!-- <input type="checkbox" class="form-control" name="palletization" id="palletization"/> -->
</div>
</div>
</div>

</div>
<div class="modal-footer">
<div class="row">
<div class="col-md-12 text-center">
<button type="submit" class="btn green-meadow save_package" >Save</button>
</div>
</div>
</div>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
</form>
