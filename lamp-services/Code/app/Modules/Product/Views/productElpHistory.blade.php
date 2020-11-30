<div class="row">
	<div class="col-md-12">
		<div class="col-md-10">&nbsp;</div>
		<div class="col-md-2">
			<a class="btn green-meadow" id="exportproductelps"  style="margin-top:5px;">Export Product ELP's</a>
			<form id="exportproductelps_form" action="{{ URL::to('/products/exportproductelpsbyproductid') }}" method="POST">
				<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
				<input type="hidden" name="product_id" value="{{$product_id}}">
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
    <div class="col-md-12">
    	
        <table id="productElpHistoryGrid" ></table>
    </div>
</div>



<style type="text/css">
	.ui-iggrid-filterrow {
		display: table-row !important;
	}

	.ui-iggrid .ui-iggrid-tablebody td {
	    padding: 10px;
	}
</style>