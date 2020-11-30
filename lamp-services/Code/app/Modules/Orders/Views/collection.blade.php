<div class="modal modal-scroll fade" id="collection" tabindex="-1" role="collection" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<h4 class="modal-title">Collection Details 
&nbsp; <strong style="color: #337bb6;">[ Balance Amount : <span class='balanceAmt'></span> ]</strong></h4>
</div>
<div class="modal-body"> 


<div class="tabbable-line">
<ul class="nav nav-tabs ">
<li class="active"><a href="#tab_15_1" data-toggle="tab"> Collection </a></li>
<li><a href="#tab_15_2" data-toggle="tab"> Collection History </a></li>

</ul>
<div class="tab-content">
<div class="tab-pane active" id="tab_15_1">
<div class="portlet-body form">
<form class="form-horizontal" role="form" id="collection_form" enctype="multipart/form-data" action="/salesorders/createCollection">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="form-body">

<div class="form-group">
<label class="col-md-3 control-label">Select Invoice <span class="required">*</span></label>
<div class="col-md-9">
<select class="form-control select2me" name="invoice" id="invoice_code" class="invoice_code">



</select>
</div>
</div>

<div class="form-group">
<label class="col-md-3 control-label">Invoice Due <span class="required">*</span></label>
<div class="col-md-9">
<input type="text" class="form-control" name="invoice_due" id="invoice_due" readonly value="">
</div>
</div>

<div class="form-group">
<label class="col-md-3 control-label">Mode Of Payment</label>
<div class="col-md-9">
<select class="form-control" name="mode_of_payment" id="mode_of_payment">
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
<input type="text" class="form-control" name="reference_num" id="reference_num">
</div>
</div>

<div class="form-group">
<label class="col-md-3 control-label">Amount</label>
<div class="col-md-9">
<input type="text" class="form-control" name="collection_amount" id="collection_amount">
</div>
</div>

<div class="form-group">
<label class="col-md-3 control-label">Collected By</label>
<div class="col-md-9">
<select class="form-control select2me" name="collected_by" id="collected_by">

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
    <input type="text" class="form-control" name="collected_on" id="collected_on" value="{{date('m/d/Y')}}" placeholder="PO Date">
</div>

</div>
</div>

<div class="form-group">
<label class="col-md-3 control-label">Remarks</label>
<div class="col-md-9">
    <input type="text" class="form-control" name="remarks" id="remarks" />
</div>
</div>


<div class="form-group">
<label class="col-md-3 control-label">Proof</label>
<div class="col-md-9">
    <input type="file" class="form-control" name="proof" id="proof" >
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



<table class="table table-striped table-bordered table-hover table-advance collectionGrid table-scrolling" style="white-space:nowrap; font-size:12px;">
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
