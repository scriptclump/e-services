
<!--<div class="row">
<div class="col-md-5">
<<div class="form-group">
<label>Brand</label>
<select class="form-control get_brand_id" id="brand_id" >
<option value="">Select Any Brand</option>
@if(!empty($brandlist))
	@foreach($brandlist as $brandName)
		<option value="{{$brandName->brand_id}}">{{$brandName->brand_name}}</option>
	@endforeach
@endif

</select>
</div>
</div>
<div class="col-md-5">
<div class="form-group">
<label>Products</label>
<select class="form-control get_brand_id" id="get_products" >
</select>
<!-- <input type="text" class="form-control" id="get_products"/>
<input type="hidden" id="related_product_id"> 
</div>
</div>
<div class="col-md-2">
<div class="form-group" style="margin-top:30px;">
	<label>&nbsp;</label>
<button type="button" class="btn btn-primary" id="add_related_product"> Add</button>
</div>
</div>
</div> -->
<div class="alert alert-success hide">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <span id="flass_message"></span>
            </div>
<div class="row">
    <div class="col-md-12">
        <table id="productsListGrid"></table>
		
    </div>
</div>
<style>
    .ui-iggrid-filterrow{display:none !important;}
    .deletechild{display:none !important;}
</style>