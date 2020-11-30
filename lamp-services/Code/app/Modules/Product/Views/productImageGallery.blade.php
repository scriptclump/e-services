<div class="col-md-4"> 

<div class="row">
<div class="col-md-12"> 
<span class="demowrap">
	 @if(isset($productData->primary_image))
          @if(preg_match('/[a-zA-Z]+:\/\/[0-9a-zA-Z;.\/?:@=_#&%~,+$]+/', $productData->primary_image, $matches))
	<img src="{{$productData->primary_image}}" class="primary_image" id="demo4" />
	@else
	<img src="{{ '/uploads/products/'.$productData->primary_image}}"  class="primary_image" id="demo4"/>
	 
     @endif
     @else
     <img src="{{ '/img/Ebutor_img_logo.jpg'}}"  class="primary_image" id="demo4"/>
     @endif
</span>
</div>
</div>

		

<ul id="demo4carousel" class="elastislide-list" style="display: block; max-height: 62px; transition: all 1500ms; transform: translateX(-8px);">
 @if(isset($productData->primary_image))
          @if(preg_match('/[a-zA-Z]+:\/\/[0-9a-zA-Z;.\/?:@=_#&%~,+$]+/', $productData->primary_image, $matches))
          
	<li class="showicons">

		<a >
			<img src="{{$productData->primary_image}}" id="gallery_primary_image" data-largeimg="{{$productData->primary_image}}" />
		</a>
		<div class="row defaulticons del-set-actions" style="display:none;">
			<!-- <div class="col-md-12 text-center">
				<span href="#0" class="btn blue md-skip"><i class="fa fa-check"></i></span>&nbsp;
				<span href="#0" class="btn red md-skip"><i class="fa fa-trash"></i></span>
			</div> -->
		</div>
	</li>
	@else
	<li class="showicons">

		<a>
			<img src="{{ '/uploads/products/'.$productData->primary_image}}" id="gallery_primary_image" data-largeimg="{{'/uploads/products/'.$productData->primary_image}}" />
		</a>
		<div class="row defaulticons del-set-actions" style="display:none;">
			<!-- <div class="col-md-12 text-center">
				<span href="#0" class="btn blue md-skip"><i class="fa fa-check"></i></span>&nbsp;
				<span href="#0" class="btn red md-skip"><i class="fa fa-trash"></i></span>
			</div> -->
		</div>
	</li>
 	@endif
  @else
  <li class="showicons">

		<a>
			<img src="{{ '/img/Ebutor_img_logo.jpg'}}" id="gallery_primary_image" data-largeimg="{{ '/img/Ebutor_img_logo.jpg'}}" />
		</a>
		<div class="row defaulticons del-set-actions" style="display:none;">
			<!-- <div class="col-md-12 text-center">
				<span href="#0" class="btn blue md-skip"><i class="fa fa-check"></i></span>&nbsp;
				<span href="#0" class="btn red md-skip"><i class="fa fa-trash"></i></span>
			</div> -->
		</div>
	</li>
  
  @endif
  <?php $i=1; ?>
  @foreach($productImages as $imageVal)
  
  	@if(preg_match('/[a-zA-Z]+:\/\/[0-9a-zA-Z;.\/?:@=_#&%~,+$]+/', $imageVal->url, $matches))

  <li class="showicons imageGallery{{$i}}">

		<a >
			<img src="{{$imageVal->url}}" data-largeimg="{{$imageVal->url}}" />
		</a>
		<div class="col-md-12 defaulticons del-set-actions" style="display:none;">
			<div class="row">
				<span href="#0" class="md-skip" onclick="setAsDefaultImage('{{$imageVal->url}}',{{$imageVal->prod_media_id}})"><i class="fa fa-check"></i></span>&nbsp;
				<span href="#0" class="md-skip" onclick="deleteProductImage({{$imageVal->prod_media_id}});"><i class="fa fa-trash"></i></span>
			</div>
		</div>
	</li>
	@else
	  <li class="showicons imageGallery{{$i}}">

		<a >
			<img src="{{ '/uploads/products/'.$imageVal->url}}"  data-largeimg="{{ '/uploads/products/'.$imageVal->url}}" />
		</a>
		<div class="col-md-12 defaulticons del-set-actions" style="display:none;">
			<div class="row">
				<span href="#0" class="md-skip" onclick="setAsDefaultImage('{{$imageVal->url}}')"><i class="fa fa-check"></i></span>&nbsp;
				<span href="#0" class="md-skip" onclick="deleteProductImage({{$imageVal->prod_media_id}});"><i class="fa fa-trash"></i></span>
			</div> 
		</div>
	</li>
	@endif
	<?php $i=$i+1;?>
	@endforeach
</ul>
<div class="row"><div class="col-md-12"><a class="btn default" data-toggle="modal" href="#basic"> Upload Images </a></div></div>
</div>
