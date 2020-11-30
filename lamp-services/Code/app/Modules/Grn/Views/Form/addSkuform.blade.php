<div id="addskurow" class="" style="display:none;">
    <div class="row" style="margin-bottom:5px;">
        <div class="col-md-6 text-left">
            <div class="caption"><strong>Add SKU </strong></div>
            <div style="display:none;" id="error-msg" class="alert alert-danger"></div>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">SKU #</label>
                <input type="text" class="form-control noEnterSubmit" name="product_sku" id="product_sku" value=""/>
                <input type="hidden" class="form-control" name="product_id" id="product_id" value=""/>
            </div>
        </div>        
        <div class="col-md-1">
            <div class="form-group">
                <label class="control-label">PO QTY</label>
                <input type="number" min="1" class="form-control noEnterSubmit" name="order_qty" id="order_qty" value="1">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">UOM</label>
                <select name="sku_uom" id="sku_uom" class="form-control noEnterSubmit">
                    <option value="">Select Pack UOM</option>
                </select>
            </div>
        </div>        
        <div class="col-md-2">
            <div class="form-group addbutt">
                <label class="control-label">&nbsp;</label>
                <button type="button" class="btn blue" id="grn_add_sku" style="margin-top:39px;">ADD</button>
            </div>
        </div>        
    </div>

</div>

@section('style')
<style type="text/css">
.addbutt{ margin-top:-39px;}
</style>
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css')}}" rel="stylesheet" type="text/css" />

@stop