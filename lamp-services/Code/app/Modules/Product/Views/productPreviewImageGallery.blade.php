<div class="col-md-4"> 

<div class="row">
<div class="col-md-12"> 
<span class="demowrap">
	 @if(isset($productData->primary_image))
          @if(preg_match('/[a-zA-Z]+:\/\/[0-9a-zA-Z;.\/?:@=_#&%~,+$]+/', $productData->primary_image, $matches))
	<img src="{{$productData->primary_image}}" id="demo4"   /> 
	@else 
	<img src="{{'/uploads/products/'.$productData->primary_image}}" class="primary_image" id="demo4"/>
	@endif
         @endif
</span>
</div>
</div>

		

<ul id="demo4carousel" class="elastislide-list">
 	@if(isset($productData->primary_image))
        @if(preg_match('/[a-zA-Z]+:\/\/[0-9a-zA-Z;.\/?:@=_#&%~,+$]+/', $productData->primary_image, $matches))
			<li class="showicons">

				<a href="#">
					<img src="{{$productData->primary_image}}" data-largeimg="{{$productData->primary_image}}" />
				</a>
				
			</li>
		@else
			<li class="showicons">

				<a href="#">
					<img src="{{ '/uploads/products/'.$productData->primary_image}}" data-largeimg="{{ '/uploads/products/'.$productData->primary_image}}" />
				</a>
				
			</li>	
		@endif
 	@endif

 	@foreach($productImages as $imageVal)
 	
	    @if(preg_match('/[a-zA-Z]+:\/\/[0-9a-zA-Z;.\/?:@=_#&%~,+$]+/', $productData->primary_image, $matches))
			<li class="showicons">

				<a href="#">
					<img src="{{$imageVal->url}}" data-largeimg="{{$imageVal->url}}" />
				</a>
				
			</li>
		@else
		 	<li class="showicons">

				<a href="#">
					<img src="{{ '/uploads/products/'.$imageVal->url}}" data-largeimg="{{ '/uploads/products/'.$imageVal->url}}" />
				</a>
				
			</li>
		
		@endif
	@endforeach
</ul>

