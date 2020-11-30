<div class="tab-pane active" id="tab_11">
    <form id="suppliersinfo" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <h3>Manufacturer Information</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Organization Name <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="organization_name" value="@if(isset($supplier_data->business_legal_name)){{$supplier_data->business_legal_name}}@endif" name="organization_name">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Organization Type <span class="required" aria-required="true">*</span></label>
                    <select class="form-control" id="organization_type" name="organization_type">
                        <option value="">Select Organization Type</option>    
                        @foreach($company_data as $companyVal )
                        @if(isset($supplier_data->business_type_id) && $companyVal->id== $supplier_data->business_type_id)
                            <option value="{{$companyVal->id}}" selected >{{$companyVal->company_type}}</option>
                        @else
                        <option value="{{$companyVal->id}}" >{{$companyVal->company_type}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Reference ERP Code </label>
                    <input type="text" class="form-control" id="reference_erp_code" value="@if(isset($supplier_data->erp_code)){{$supplier_data->erp_code}}@endif"   name="reference_erp_code">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Year Of Establishment <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="date_estb" value="@if(isset($supplier_data->est_year)){{$supplier_data->est_year}}@endif" name="date_estb">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Website <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="org_site" value="@if(isset($supplier_data->website_url)){{$supplier_data->website_url}}@endif"  name="org_site">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Logo <span class="required" aria-required="true">*</span></label>
                        <div class="row">
                            <div class="col-md-12">  

<div class="fileinput fileinput-new" data-provides="fileinput" style="margin-left:16px !important;">
<span class="btn default btn-file btn green-meadow" style="width:110px !important;">

<span class="fileinput-new">Choose File </span>
<span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>


</span>
<?php
$bp = url('uploads/Suppliers_Docs');
$base_path = $bp."/";
?>
<span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
<div class="fileinput-preview fileinput-exists thumbnail " style="width: 100px; height: 33px; margin-left:9px;">  <img src="@if(isset($supplier_data->logo)){{$base_path.$supplier_data->logo}}@endif" class="org_edit_file" alt="" /></div>


<br />
<input id="org_file" type="file"  name="org_file" style="margin-top: -27px !important;  position: absolute;opacity: 0;"/>

<span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>


<div class="fileinput-new thumbnail" style="width: 100px; height: 33px; display:none;  ">
<img src="@if(isset($supplier_data->logo)){{$base_path.$supplier_data->logo}}@endif" alt="" id="org_supplier_logo"/>
</div>
</div>



                        </div>
                    </div>
                </div>
            </div>

        </div>
        <h3>Contact Information</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">First Name <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="org_firstname" value="@if(isset($supplier_data->firstname)){{$supplier_data->firstname}}@endif" name="org_firstname">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Last Name <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->lastname)){{$supplier_data->lastname}}@endif" id="org_lastname" name="org_lastname">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Email <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->email_id)){{$supplier_data->email_id}}@endif" id="org_email" name="org_email">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Mobile <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="org_mobile" value="@if(isset($supplier_data->mobile_no)){{$supplier_data->mobile_no}}@endif" name="org_mobile" maxlength="11">
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label class="control-label">Landline </label>
                            <input type="text" class="form-control" value="@if(isset($supplier_data->landline_no)){{$supplier_data->landline_no}}@endif"  id="org_landline" name="org_landline">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="control-label">EXT Number </label>
                            <input type="text" class="form-control" value="@if(isset($supplier_data->landline_ext)){{$supplier_data->landline_ext}}@endif"  id="org_extnumber" name="org_extnumber">
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
        <hr>
        <div class="row">
            <div class="col-md-12 text-center">
				@if(isset($approve_access) && $approve_access == 1)
				<button type="button" class="btn green-meadow" id="app_supp_info" name="{{$supplier_data->supplier_id}}">Approve</button>
				@endif
				@if(isset($reject_access) && $reject_access == 1)
				<button type="button" data-toggle="modal" href="#rej_supp_pop" class="btn green-meadow" id="rej_supp_info">Reject</button>
				@endif				
                <button type="submit" class="btn green-meadow btnn supp_info" id="supp_info">Save & Continue</button>
                <button type="button" id="cancelmaninfo" class="btn green-meadow">Cancel</button>
            </div>
        </div>
    </form>
</div>