
<div class="row prod-inform"> 
                                        	<div class="col-lg-12">
                                        
		<table class="table table-bordered table-advance ">
			<thead>
				<tr>
					<th class="col-md-3" colspan="2">
						<b>PRODUCT CHARACTERTICS</b>
					</th>
				 
				</tr>
			</thead>
			<tbody>
			<tr> 
				<td class="col-md-3"> Perishable </td>
				<td > 
					<div class="col-md-6">
					<select class="form-control"  name="perishable">
							@if($getProductCharacterstics['perishable']== 1)
								<option value="1" selected>Yes</option>
								<option value="0">No</option>
							@elseif($getProductCharacterstics['perishable']== 0)
								<option value="0" selected>No</option>
								<option value="1" >Yes</option>
							@else	
								<option value="">Please Select</option>
								<option value="1">Yes</option>
								<option value="0">No</option>
							@endif
					</select>
					</div>
				</td>
				</tr>
				<tr> 
					<td> Product Form </td>
					<td >
					 <div class="col-md-6">
					 	
					 	<select class="form-control"  name="product_form">
					 		<option value="0">Please select</option>
					 		@foreach($product_form_data as $formValue)
					 		@if($getProductCharacterstics['product_form']==$formValue->value)
								<option value="{{$formValue->value}}" selected>{{$formValue->name}}</option>
								@else
								<option value="{{$formValue->value}}" >{{$formValue->name}}</option>
								@endif
							@endforeach
						</select>
					 </div>
					</td>
				</tr>
				<tr> 
					<td> Flammable </td>
					<td >
					 <div class="col-md-6">
					 	<select class="form-control"  name="flammable">
					 		@if($getProductCharacterstics['flammable']== 1)
									<option value="1" selected>Yes</option>
									<option value="0">No</option>
								@elseif($getProductCharacterstics['flammable']== 0)
									<option value="0" selected>No</option>
									<option value="1" >Yes</option>
								@else	
									<option value="">Please Select</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								@endif
						</select>
					 </div>
					</td>
				</tr>
				<tr> 
					<td> Hazardous </td>
					<td >
					 <div class="col-md-6">
					 	<select class="form-control"  name="hazardous">
					 		@if($getProductCharacterstics['hazardous']== 1)
									<option value="1" selected>Yes</option>
									<option value="0">No</option>
								@elseif($getProductCharacterstics['hazardous']== 0)
									<option value="0" selected>No</option>
									<option value="1" >Yes</option>
								@else	
									<option value="">Please Select</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								@endif
						</select>
					 </div>
					</td>
				</tr>
				<tr> 
					<td> Odour</td>
					<td > 
						<div class="col-md-6">
							<select class="form-control"  name="odour">
							@if($getProductCharacterstics['odour']== 1)
									<option value="1" selected>Yes</option>
									<option value="0">No</option>
								@elseif($getProductCharacterstics['odour']== 0)
									<option value="0" selected>No</option>
									<option value="1" >Yes</option>
								@else	
									<option value="">Please Select</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								@endif
							</select>
						</div>
					</td>
				</tr>
				<tr> 
					<td> Fragile </td>
					<td > 
						<div class="col-md-6">
							<select class="form-control"  name="fragile">
								@if($getProductCharacterstics['fragile']== 1)
									<option value="1" selected>Yes</option>
									<option value="0">No</option>
								@elseif($getProductCharacterstics['fragile']== 0)
									<option value="0" selected>No</option>
									<option value="1" >Yes</option>
								@else	
									<option value="">Please Select</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								@endif
							</select>

						</div>
					</td>
				</tr> 	
				<tr>
				 <td> License Required</td>
				 <td > 
				 	<div class="col-md-6">
				 		<select class="form-control"  name="license_required">
				 			@if($getProductCharacterstics['licence_req'] == 1)
									<option value="1" selected>Yes</option>
									<option value="0">No</option>
								@elseif($getProductCharacterstics['licence_req']== 0)
									<option value="0" selected>No</option>
									<option value="1" >Yes</option>
								@else	
									<option value="">Please Select</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								@endif
						</select>
				 	</div>
				 </td>
				</tr>
				<tr> 
					<td> License Type </td>
					<td > 
						<div class="col-md-6">
							<select class="form-control"  name="license_type">
								<option value="">Please Select...</option>
						 		@foreach($license_typ_data as $dataValue)
								@if($getProductCharacterstics['licence_type']==$dataValue->value)
								<option value="{{$dataValue->value}}" selected>{{$dataValue->name}}</option>
								@else
								<option value="{{$dataValue->value}}" >{{$dataValue->name}}</option>
								@endif
								@endforeach
							</select>
						</div>
					</td>
				</tr> 	
				<tr>
				 <td> Shelf Life</td>
				 <td >
				  <div class="col-md-6">
				  	<input type="number"  class="form-control" min="0" name="shelf_life" value="@if(isset($productData->shelf_life)){{ $productData->shelf_life}}@endif">
				  </div>
				</td>
				</tr>
				<tr>
				 <td> Shelf Life UOM </td>
				 <td > 
				 	<div class="col-md-6">
				 		<select class="form-control"  name="shelf_life_uom">
				 				<option value="">Please Select</option>
				 				@if(isset($shelf_uom_data))
						 		@foreach($shelf_uom_data as $dataValue)
								@if($productData->shelf_life_uom==$dataValue->value)
								<option value="{{$dataValue->value}}" selected>{{$dataValue->name}}</option>
								@else
								<option value="{{$dataValue->value}}" >{{$dataValue->name}}</option>
								@endif
								@endforeach
								@endif
							</select>
				 	</div>
				 </td>
				</tr> 
				<tr>
				 <td> Bin Category Type</td>
				 <td > 
				 	<div class="col-md-6">
				 		<select class="form-control"  name="bin_category_type">
				 				<option value="">Please Select</option>
				 				@if(isset($bin_category_type))
						 		@foreach($bin_category_type as $dataValue)
								@if($getProductCharacterstics['bin_category_type']==$dataValue->value)
								<option value="{{$dataValue->value}}" selected>{{$dataValue->name}}</option>
								@else
								<option value="{{$dataValue->value}}" >{{$dataValue->name}}</option>
								@endif
								@endforeach
								@endif
							</select>
				 	</div>
				 </td>
				</tr> 		
			</tbody>
		</table>

		</div>
</div>
<div class="row prod-inform"> 
    <div class="col-lg-12">
    	<table class="table table-bordered table-advance ">
			<thead>
				<tr>
					<th class="col-md-3" colspan="2">
						<b>PRODUCT POLICIES</b>
					</th>				 
				</tr>
			</thead>
			<tbody>
				<tr>
				 <td class="col-md-3"> Warranty Policy </td>
				 <td > 
				 	<div class="col-md-6">
				 		<input type="text"  class="form-control" id="warranty_policy" name="warranty_policy" value="@if(isset($warranty_policy)){{ $warranty_policy}}@endif">
				 	</div>
				 </td>
				</tr> 	
				<tr>
				 <td class="col-md-3"> Return Policy </td>
				 <td > 
				 	<div class="col-md-6">
				 		<input type="text" id="return_policy" class="form-control"  name="return_policy" value="@if(isset($return_policy)){{$return_policy}}@endif">
				 	</div>
				 </td>
				</tr> 		
			</tbody>
		</table>
	</div>
</div>



                                        <div class="row"> 
                                        	<input type="hidden" id="permission_level" value="{{$supplier_login_permissions}}">
                                        	<div class="col-lg-12" id="add_att_with_group">
                                        		
                              
  
           
</div>
<hr>
<div class="row text-center">
        <button class="btn green btn-sm" type="button" id="edit_product_button" >Save  </button>
     <!--    <button class="btn green btn-sm" type="button" id="editproduct_sumbit">Submit</button> -->
        <a href="/products" class="cancel_btn" ><button type="button" id="cancelmaninfo" class="btn green btn-sm">Cancel</button></a>
    </div> 
</div>

@section('style')
<style type="text/css">

</style>
@stop
                                        
             