<!-- <div class="row"><div class="col-lg-11">
<p><b>Product Description</b></p>

 <p>@if(isset($productData->product_content_model->description)) {{$productData->product_content_model->description}} @endif </p>
  </div>
   
    <div class="col-lg-1 pull-right text-center">
    	<a href="javascript:void(0)" class="click" data-id="pop1">
    		<i class="fa fa-comment-o" aria-hidden="true"></i>
    	</a>(<span id="comments_count"></span>)
        <div class=" pop_holder " id="pop1" >
			<div class="dropdown-menu-list scroller"> 
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 add_comment_text" > 
				</div>
			</div>
		</div>
  	</div>                 
</div>
 -->

  <div class="portlet">

<div class="portlet-body"> 
<table class="table table-bordered table-advance ">
<thead>
<tr>
<th class="col-md-3">
<b>PRODUCT CHARACTERTICS</b>
</th>
<th>

</th>


</tr>
</thead>
<tbody>

<tr> 
	<td> Perishable </td>
	<td >@if($getProductCharacterstics['perishable']== 1)
					<span>Yes</span>
				@else
					<span>No</span>
					@endif
</td>
</tr>
<tr> 
	<td> Product Form </td>
	<td >@foreach($product_form_data as $formValue)
	 		@if($getProductCharacterstics['product_form']==$formValue->value)
				<span>{{$formValue->name}}</span>
				
				@endif
			@endforeach
</tr>
<tr> 
	<td> Flammable </td>
	<td >@if($getProductCharacterstics['flammable']== 1)
					<span>Yes</span>
				@else
					<span>No</span>
					@endif</td>
</tr>
<tr> 
	<td> Hazardous </td>
	<td >@if($getProductCharacterstics['hazardous']== 1)
					<span>Yes</span>
				@else
					<span>No</span>
					@endif</td></td>
</tr>
<tr> 
	<td> Odour</td>
	<td >@if($getProductCharacterstics['odour']== 1)
					<span>Yes</span>
				@else
					<span>No</span>
					@endif</td>
</tr>
<tr>
 <td> Fragile </td>
 <td >@if($getProductCharacterstics['fragile']== 1)
					<span>Yes</span>
				@else
					<span>No</span>
					@endif</td>
</tr> 	
<tr>
 <td> License Required</td>
 <td >@if($getProductCharacterstics['licence_req']== 1)
					<span>Yes</span>
				@else
					<span>No</span>
					@endif</td>
</tr>
<tr> 
	<td> License Type </td>
	<td >@foreach($license_typ_data as $dataValue)
				@if($getProductCharacterstics['licence_type']==$dataValue->value)
				<span>{{$dataValue->name}}</span>
				@endif
				@endforeach</td>
</tr> 	
<tr> 
	<td> Shelf life</td>
	<td >{{$productData->shelf_life}}</td>
</tr>
<tr>
 <td> Shelf Life UOM </td>
 <td >@foreach($shelf_uom_data as $dataValue)
				@if($productData->shelf_life_uom==$dataValue->value)
				<span>{{$dataValue->name}}</span>
				@endif
				@endforeach</td>
</tr> 	
<tr>
 <td> Bin Category Type </td>
 <td >@foreach($bin_category_type as $dataValue)
				@if($getProductCharacterstics['bin_category_type']==$dataValue->value)
				<span>{{$dataValue->name}}</span>
				@endif
				@endforeach</td>
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
				 		@if(isset($warranty_policy)){{ $warranty_policy}}@endif
				 	</div>
				 </td>
				</tr> 	
				<tr>
				 <td class="col-md-3"> Return Policy </td>
				 <td > 
				 	<div class="col-md-6">
				 	@if(isset($return_policy)){{$return_policy}}@endif
				 	</div>
				 </td>
				</tr> 		
			</tbody>
		</table>
	</div>
</div>

<div class="row"> 
	<div class="col-lg-12">

<div class="portlet">
	@foreach($getAttributeGroup as $productAttGroupValue)

<div class="portlet-body">
 
<table class="table table-bordered table-advance table_rel ">
<thead>
<tr>
<th class="col-md-3">
 <b>{{$productAttGroupValue->name}}</b>
</th>
<th>

</th>


</tr>
</thead>
<tbody>
								 @foreach($attributeIdValueData as $productAttVal)
								 @if($productAttVal->attribute_group_id==$productAttGroupValue->attribute_group_id)
								<tr> <td> {{$productAttVal->name}} </td>
								<td > {{$productAttVal->value}}   <div class="col-lg-1 pull-right text-center"></div> </td></tr>
								 @endif
						 		@endforeach
								</tbody>
</table>
 
</div>
@endforeach  
</div>
                                                                                                                                        
<hr>

                                          <!-- <div class="row text-center">                                                                                                                                <button class="btn green btn-sm" type="button">Approve</button>
<button class="btn red btn-sm" type="button">Disapprove</button>
<button class="btn green btn-sm" type="button">Close</button>
                                         </div> -->
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                                                                                                                                                                                            
                                                                                                                                                                                                                                                                            
                                                                                                                                                                                                                                                                                                                                                                                                                                            
                             
                                                                                                         



</div></div>
