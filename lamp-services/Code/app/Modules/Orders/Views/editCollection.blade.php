<div class="modal modal-scroll fade" id="editCollection" tabindex="-1" role="editCollection" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<h4 class="modal-title">Edit Collection Details</h4>
</div>
<div class="modal-body"> 


<div class="tabbable-line">
<div class="tab-content">
<div class="tab-pane active" id="tab_15_1">
<div class="portlet-body form">
<form class="form-horizontal" role="form" id="edit_collection_form" enctype="multipart/form-data" action="/salesorders/editCollection">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="form-body">

<div class="form-group">
<label class="col-md-3 control-label">Invoice <span class="required">*</span></label>
<div class="col-md-9">
<input type="text" class="form-control" name="edit_coll_invoice_code" id="edit_coll_invoice_code" readonly value="">

<input type="hidden" class="form-control" name="edit_coll_collection_id" id="edit_coll_collection_id" />
<input type="hidden" class="form-control" name="edit_coll_collection_history_id" id="edit_coll_collection_history_id" />
</div>
</div>

<div class="form-group">
<label class="col-md-3 control-label">Mode Of Payment</label>
<div class="col-md-9">
<select class="form-control" name="edit_coll_mode_of_payment" id="edit_coll_mode_of_payment">
@if(isset($paymentModesArr) && is_array($paymentModesArr))
@foreach($paymentModesArr as $value=>$name)
<option value="{{ $value }}">{{ $name }}</option>
@endforeach
@endif
</select>
</div>
</div>

<div class="form-group">
<label class="col-md-3 control-label">Reference Number</label>
<div class="col-md-9">
<input type="text" class="form-control" name="edit_coll_reference_num" id="edit_coll_reference_num">
</div>
</div>

<div class="form-group">
<label class="col-md-3 control-label">Amount</label>
<div class="col-md-9">
<input type="text" class="form-control" name="edit_coll_collection_amount" id="edit_coll_collection_amount" readonly>
</div>
</div>

<div class="form-group">
<label class="col-md-3 control-label">Collected By</label>
<div class="col-md-9">
<select class="form-control select2me" name="edit_coll_collected_by" id="edit_coll_collected_by" readonly>

@if(isset($deliveryExecutives) && is_array($deliveryExecutives))
@foreach($deliveryExecutives as $User)
<option value="{{ $User->user_id }}">{{ $User->firstname.' '.$User->lastname }}</option>
@endforeach
@endif

</select>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-3">Collected On</label>
<div class="col-md-9">

<div class="input-icon right">
    <i class="fa fa-calendar"></i>
    <input type="text" class="form-control" name="edit_coll_collected_on" id="edit_coll_collected_on" value="{{date('m/d/Y')}}" readonly >
</div>

</div>
</div>

<div class="row">
<div class="col-md-12 text-center">
<button type="submit" class="btn green">Submit</button>
</div>
</div>
</div>
</form>
</div>
</div>
<div class="tab-pane" id="tab_15_2">
<div class="table-responsive">



<table class="table table-striped table-bordered table-hover table-advance collectionGrid" style="white-space:nowrap; font-size:12px;">
</table>



</div>
</div>

</div>
</div>






</div>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
