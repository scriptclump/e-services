<div class="tab-pane" id="tab_22_1">
<form id="agr_terms" name="agr_terms" method="POST" enctype="multipart/form-data">
<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

@if($vendor == 'Supplier')

<h4>Purchase Terms</h4>
<div class="row">
<div class="col-md-4">
<div class="form-group">
<label class="control-label">Vendor Registration Charges</label>
<input type="text" class="form-control" id="vendorreg_charges" value="@if(isset($supllier_terms->vendor_reg_charges)){{$supllier_terms->vendor_reg_charges}}@endif" name="vendorreg_charges">
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label class="control-label">SKU Registration Charges </label>
<input type="text" class="form-control" id="skureg_charges" value="@if(isset($supllier_terms->sku_reg_charges)){{$supllier_terms->sku_reg_charges}}@endif" name="skureg_charges">
</div>
</div> 
<div class="col-md-4">
<div class="form-group">
<label class="control-label">DC Linking Charges</label>
<input type="text" class="form-control" id="dclinking_charges" value="@if(isset($supllier_terms->dc_link_charges)){{$supllier_terms->dc_link_charges}}@endif" name="dclinking_charges">
</div>
</div>
</div>

<div class="row">
<div class="col-md-4">
<div class="form-group">
<label class="control-label">B2B Channel Support Assistance</label>
<input type="text" class="form-control" id="btbchannel_supportassistance" value="@if(isset($supllier_terms->b2b_channel_support_as)){{$supllier_terms->b2b_channel_support_as}}@endif" name="btbchannel_supportassistance">
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label class="control-label">ECP Visibility Assistance</label>
<input type="text" class="form-control" id="ecp_visibilityassistance" value="@if(isset($supllier_terms->ecp_visibility_ass)){{$supllier_terms->ecp_visibility_ass}}@endif" name="ecp_visibilityassistance">
</div>
</div> 
<div class="col-md-4">
<div class="form-group">
<label class="control-label">PO Days</label>
<?php $po_days = array(); if(isset($supllier_terms->po_days)) { $po_days = explode(",",$supllier_terms->po_days); } ?>
                    <select class="form-control multi-select-search-box common" multiple="multiple" placeholder='Select PO Days' id="po_days" name="po_days">
                        <option value="SUNDAY" <?php if(in_array('SUNDAY', $po_days)) { echo "selected" ;} ?>>SUNDAY</option>    
                        <option value="MONDAY" <?php if(in_array('MONDAY', $po_days)) { echo "selected" ;} ?>>MONDAY</option>
                        <option value="TUESDAY" <?php if(in_array('TUESDAY', $po_days)) { echo "selected" ;} ?>>TUESDAY</option>
                        <option value="WEDNESDAY" <?php if(in_array('WEDNESDAY', $po_days)) { echo "selected" ;} ?>>WEDNESDAY</option>
                        <option value="THURSDAY" <?php if(in_array('THURSDAY', $po_days)) { echo "selected" ;} ?>>THURSDAY</option>
                        <option value="FRIDAY" <?php if(in_array('FRIDAY', $po_days)) { echo "selected" ;} ?>>FRIDAY</option>
                        <option value="SATURDAY" <?php if(in_array('SATURDAY', $po_days)) { echo "selected" ;} ?>>SATURDAY</option>
                    </select>
</div>
</div>
</div>

<h4>Delivery Terms</h4>
<div class="row">
    <div class="col-md-4">
        <div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Delivery TAT</label>
<input type="text" class="form-control" id="delivery_tat" value="@if(isset($supllier_terms->delivery_tat)){{$supllier_terms->delivery_tat}}@endif" name="delivery_tat">
</div>
</div> 
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Delivery TAT UoM</label>
<select class="form-control" id="delivery_tatuom" name="delivery_tatuom">
                        <option value="">Select Delivery TAT UoM</option>    
                            @foreach($uom_data as $uomVal )
                            @if(isset($supllier_terms->delivery_tat_uom) && $supllier_terms->delivery_tat_uom==$uomVal->value)
                            <option value="{{$uomVal->value}}"  selected>{{$uomVal->account_type}}</option>
                            @else
                            <option value="{{$uomVal->value}}" >{{$uomVal->account_type}}</option>
                            @endif
                            @endforeach
                    </select>
</div>
</div>
            </div>
</div>
<div class="col-md-4">
<div class="form-group">
<label class="control-label">Delivery Frequency (within Week)</label>
<input type="text" class="form-control" id="delivery_frequency" value="@if(isset($supllier_terms->delivery_frequency)){{$supllier_terms->delivery_frequency}}@endif" name="delivery_frequency">
</div>
</div>

</div>

<h4>Payment & Credit Terms</h4>
<div class="row"> 
<div class="col-md-4">
<div class="form-group">
<label class="control-label">Invoice Days</label>
<?php $invoice_days = array(); if(isset($supllier_terms->invoice_days)) { $invoice_days = explode(",",$supllier_terms->invoice_days); } ?>
 <select class="form-control multi-select-search-box common" multiple="multiple" placeholder='Invoice Days' id="invoice_days" name="invoice_days">
                        <option value="SUNDAY" <?php if(in_array('SUNDAY', $invoice_days)) { echo 'selected' ;} ?>>SUNDAY</option>    
                        <option value="MONDAY" <?php if(in_array('MONDAY', $invoice_days)) { echo 'selected' ;} ?>>MONDAY</option>
                        <option value="TUESDAY" <?php if(in_array('TUESDAY', $invoice_days)) { echo 'selected' ;} ?>>TUESDAY</option>
                        <option value="WEDNESDAY" <?php if(in_array('WEDNESDAY', $invoice_days)) { echo 'selected' ;} ?>>WEDNESDAY</option>
                        <option value="THURSDAY" <?php if(in_array('THURSDAY', $invoice_days)) { echo 'selected' ;} ?>>THURSDAY</option>
                        <option value="FRIDAY" <?php if(in_array('FRIDAY', $invoice_days)) { echo 'selected' ;} ?>>FRIDAY</option>
                        <option value="SATURDAY" <?php if(in_array('SATURDAY', $invoice_days)) { echo 'selected' ;} ?>>SATURDAY</option>
                    </select>
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label class="control-label">Credit Period (Days)  <span class="required" aria-required="true">*</span></label>
<input type="text" class="form-control" id="credit_period" value="@if(isset($supllier_terms->credit_period)){{$supllier_terms->credit_period}}@endif" name="credit_period">
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label class="control-label">Payment Days</label>
<select id="payment_days" name="payment_days" class="form-control">
                                <option value="" >Select Payment Days</option>
    @foreach($getpayment_days as $daysVal )
                            @if(isset($supllier_terms->payment_days) && $supllier_terms->payment_days==$daysVal->id)
                            <option value="{{$daysVal->id}}"  selected>{{$daysVal->account_type}}</option>
                            @else
                            <option value="{{$daysVal->id}}" >{{$daysVal->account_type}}</option>
                            @endif
                            @endforeach
                        
                    </select>

</div>
</div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
<label class="control-label">Negotiation</label>
<?php $neg = array(); if(isset($supllier_terms->negotiation)) { $neg = explode(",",$supllier_terms->negotiation); } ?>
                    <select id="negotiation" name="negotiation" class="form-control multi-select-search-box common" multiple="multiple" placeholder='Select Negotiation'>
                        @foreach($negotiation_data as $negVal )
                            <?php
                            $class='';
                            if(in_array($negVal->value,$neg)){
                                $class='selected';
                            }?>
                            <option value="{{$negVal->value}}" <?php echo $class ?> >{{$negVal->account_type}}</option>
                            @endforeach
                        
                    </select>
</div>
        
    </div>
    
</div>


<h4>Return Terms</h4>
<div class="row"> 
<div class="col-md-4">
    <div class="form-group">
<label class="control-label">RTV <span data-original-title="Return To Vendor" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
<div class="form-control radioborder">
@if(isset($supllier_terms->rtv))
                    <input type="radio" name="rtv" id="rtv" value="1" <?php if($supllier_terms->rtv == 1) { echo "checked"; } ?>> Yes
                    <input type="radio" name="rtv" id="rtv" value="0" <?php if($supllier_terms->rtv == 0) { echo "checked"; } ?>> No
                    @else
                    <input type="radio" name="rtv" id="rtv" value="1" checked> Yes
                    <input type="radio" name="rtv" id="rtv" value="0"> No
                    @endif
</div>
</div>

</div>
<div class="col-md-4">
<div class="form-group">
<label class="control-label">RTV Timeline</label>
<input type="text" class="form-control" id="rtv_timeline" value="@if(isset($supllier_terms->rtv_timeline)){{$supllier_terms->rtv_timeline}}@endif" name="rtv_timeline">
</div>
</div> 
<div class="col-md-4">
<div class="form-group">
<label class="control-label">RTV Scope</label>
<select class="form-control" id="rtv_scope" name="rtv_scope">
                        <option value="">Select RTV Location</option>
                    @foreach($rtv_scope as $rtvVal )
                            @if(isset($supllier_terms->rtv_scope) && $supllier_terms->rtv_scope==$rtvVal->value)
                            <option value="{{$rtvVal->value}}"  selected>{{$rtvVal->account_type}}</option>
                            @else
                            <option value="{{$rtvVal->value}}" >{{$rtvVal->account_type}}</option>
                            @endif
                            @endforeach
                   </select>
</div>
</div>
</div>

<div class="row">
<div class="col-md-4">
<div class="form-group">
<label class="control-label">RTV location</label>
<select class="form-control" id="rtv_location" name="rtv_location">
                        <option value="">Select RTV Location</option>  
                    @foreach($rtvloc_data as $rtvVal )
                            @if(isset($supllier_terms->rtv_location) && $supllier_terms->rtv_location==$rtvVal->value)
                            <option value="{{$rtvVal->value}}"  selected>{{$rtvVal->account_type}}</option>
                            @else
                            <option value="{{$rtvVal->value}}" >{{$rtvVal->account_type}}</option>
                            @endif
                            @endforeach
                    </select>
</div>
</div>

</div>
@endif
<h4>Agreement Dates</h4>
<div class="row">   
<div class="col-md-4">
<div class="form-group">
<label class="control-label">Start Date</label>
<input type="text" class="form-control" id="start_date" value="@if(isset($supllier_terms->start_date)){{$supllier_terms->start_date}}@endif" name="start_date">
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label class="control-label">End Date</label>
<input type="text" class="form-control" id="end_date" value="@if(isset($supllier_terms->end_date)){{$supllier_terms->end_date}}@endif" name="end_date">
</div>
</div>                
</div>

<br>
<?php
$cancel = str_replace(' ','_',$vendor).'_aggr_cancel';
?>
<div class="row">
<div class="col-md-12 text-center">
<button type="submit" class="btn green-meadow btnn supp_terms" id="supp_agrtrms_info">Save</button>
<button type="button" id="{{$cancel}}" class="btn green-meadow">Cancel</button>
</div>
</div>    
</form>
</div>

